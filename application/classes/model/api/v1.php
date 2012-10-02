<?php defined('SYSPATH') or die('No direct script access');

/**
 * This provides common API methods for API models to use as a 'mixin', allowing 
 * API models to extend Mundo for data manipulation whilst adding API-specific 
 * properties and methods.
 *
 * @cateogry API
 * @subcategory API Version 1
 */
trait Model_API_V1
{

	/**
	 * Ensures we only load resources belonging to the resource parent.
	 *
	 * For example, if we load /sites/$id/themes we'd only want to load themes 
	 * for that specific site.
	 *
	 * This needs to be handled by each individual API class to take care of 
	 * authorisation and permission issues.
	 *
	 * @var array  Array of model keys to resource IDs in order of the URI
	 * @return $this
	 */
	abstract public function init($resource_chain = array());

	/**
	 * Loads a collection or resource from the DB
	 *
	 * @param  array   Search or Fields modifiers to search or retrieve partial 
	 *                 data respectively
	 * @return mixed   Model_API_V1_$Model or Mundo_Collection
	 * @throws App_API_Exception  On any failure with the appropriate HTTP code 
	 * as the exception code.
	 */
	public function API_Get($params = array())
	{
		$this->_initialise_search(Arr::get($params, 'search'));

		$fields = $this->_configure_returned_fields(Arr::get($params, 'fields'));

		// This is set in the 'resource' controller using the ID URI parameter
		if ($this->get('_id') !== NULL)
		{
			$this->load($fields);

			if ( ! $this->loaded())
				throw new App_API_Exception("We could not load the requested resource. Please check the request parameters to ensure the resource exists or ensure you are authorised to access the resource.", NULL, 404);

			return array(
				'content' => $this->original(),
				'metadata' => array('status' => 200) + $this->metadata()
			);
		}

		// No ID is set so we return an array of results, even if there is only 
		// one result
		$cursor = $this->find($fields)->limit(20);

		$return = array(
			'content' => array(),
			'metadata' => array('status' => 200) + $this->metadata() + array('results' => $cursor->count())
		);

		foreach($cursor as $item)
		{
			$return['content'][] = $item->original();
		}

		return $return;
	}

	/**
	 * Updates a resource, overwriting publicly writable fields with the 
	 * provided $data.
	 *
	 * This resource is used to update resources instead of POST because PUT is 
	 * idempotent. Because IDs are automatically generated by the server (us) we 
	 * cannot use PUT to create in an idempotent manner.
	 *
	 * @param array     Array of data to save in the model
	 * @return boolean  True on success
	 * @throws App_API_Exception  on any failure
	 */
	public function API_Put($data)
	{
	}

	/**
	 * Creates a resource using the provided data.
	 *
	 * @param array     Array of data to save in the new model
	 * @return boolean  True on success
	 * @throws App_API_Exception  on any failure
	 */
	public function API_Post($data)
	{
		var_dump($data);
	}

	/**
	 * Updates specific fields in a resource in a PARTIAL manner. This does not 
	 * do a full update, which could be better for mobile devices.
	 *
	 * @param array     Array of $fields => $data to overwrite
	 * @return boolean  True on success
	 * @throws App_API_Exception  on any failure
	 */
	public function API_Patch($field)
	{
	}

	/**
	 * Deletes a resource from the database.
	 *
	 * This is a permanent action.
	 *
	 * @return boolean True on success
	 * @throws App_API_Exception
	 */
	public function API_Delete()
	{
	}

	/**
	 * Lists which of the above methods are available for the specific collection
	 * or resource
	 *
	 */
	public function API_Options()
	{
	}

	/**
	 * Returns metadata regarding the collection or loaded resource.
	 *
	 * @return array  array of collection/resource metadata
	 */
	public function metadata()
	{
		// Remove our hidden fields from the list of fields returned in the 
		// metadata
		$hidden = array_keys($this->_private_fields);
		$fields = array_diff($this->_fields, $hidden);

		// When JSON encoding a diffed array the numerical indexes are also 
		// encoded. We only want a basic object such as {'id','name'}, not 
		// {'0':'id','1':'name'}. This stops that.
		$fields = array_values($fields);

		return array(
			'uri'           => Request::$current->uri(),
			'request_time'  => gmdate("Y-m-d\TH:i:s\Z", $_SERVER['REQUEST_TIME']),
			'response_time' => gmdate("Y-m-d\TH:i:s\Z", time()),
			'schema'        => array(
				'fields'      => $fields,
				'binary'      => $this->_binary_fields,
				'read_only'   => $this->_read_only_fields,
			),
			'children'      => $this->_child_resources,
		);

	}

	/**
	 * Because some models have private data (such as roles, passwords, plans, 
	 * plan costs etc.) we need to hide this data from an API call.
	 *
	 * This method configures the return fields, filtering out private fields.
	 *
	 * This method is also used when retrieving a partial resource, such as
	 * selecting only the 'name' field.
	 *
	 * @param  string|array  Comma-separated string or array of partial fields 
	 *                       to display
	 * @return array         Array in the MongoPHP format to show/hide fields
	 */
	protected function _configure_returned_fields($fields = array())
	{
		if (empty($fields))
		{
			return array_fill_keys(array_values($this->_private_fields), '0');
		}

		if ( ! is_array($fields))
		{
			$fields = explode(',', (string) $fields);
		}

		// Loop through the fields to return and set their status to '1', which 
		// is the argument to return the field in MongoDB
		$matched_fields = array();
		foreach ($fields as $fields)
		{
			$matched_fields[$field] = 1;
		}

		$fields = array_merge($matched_fields, $this->_private_fields);

		// Ensure that we're not mixing including and excluding fields - MongoDB 
		// can't handle this. If there are mixes remove all hidden fields, as 
		// MongoDB is only going to return the ones we ask for
		if (in_array(1, $fields) && in_array(0, $fields))
		{
			foreach ($fields as $item => $value)
			{
				if ($value == 0) unset($fields[$item]);
			}
		}

		return $fields;
	}

	/**
	 * Helper method for API_Get which traverses all search parameters and sets 
	 * the respective data ready for the find/load methods.
	 *
	 * @param string  Search parameters in the formmat of 
	 *                'field1:value1,field2:value2'
	 * @return $this
	 */
	protected function _initialise_search($terms)
	{
		if ( ! $terms)
			return $this;

		// Searching is done in the format of 'field:value,field:value'
		// IE. a comma splits search terms
		$searched_fields = explode(',', $terms);

		foreach ($searched_fields as $item)
		{
			list($field, $term) = explode(':', $item);

			// Ensure these aren't hidden fields which, by default, aren't accessible 
			// through the API, so disregard them even in a GET call.
			if (array_key_exists($field, $this->_hidden_fields))
				continue;

			$this->set($field, $term);
		}

		return $this;
	}

}
