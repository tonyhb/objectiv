<?php defined('SYSPATH') or die('No direct script access.');

abstract class App_Model extends Mundo_Object
{

	/**
	 * The name of the parent collection used when accessing the object from an 
	 * API. A value of NULL indicates this has no parent, such as an account 
	 * object.
	 *
	 * This stores the name of the object as represented in the app URI ['uri'] 
	 * and its column name in MongoDB ['mongo']
	 *
	 * @var array
	 */
	protected $_parent_coll = NULL;

	/**
	 * These are fields that aren't returned in a standard API call, formatted 
	 * according to the Mongo standard
	 *
	 * @var array
	 */
	protected $_hidden_fields = array();

	/**
	 * Used in API requests to ensure that models are only loaded according to 
	 * the parent specified in the URI.
	 *
	 * This allows us to chain relativity into the API URI. Take this example:
	 *
	 *   /sites/{id}/themes/{id}/css/{id}
	 *
	 * This loads a specific CSS file in a specific theme for a specific site. 
	 * If either the theme ID or the site ID aren't a match the model returns 
	 * a 404.
	 *
	 * @param array   Array of arrays containing the parent's model name and ID.
	 * @return this
	 */
	public function set_parent($parent = array())
	{
		if ($this->_parent_coll === NULL)
			return;

		// Remove any empties
		$parent = array_filter($parent);

		if (Arr::is_assoc($this->_parent_coll))
		{
			// This is an associative array which means the parent coll property 
			// only lists one parent, not an array of parents. Make this an 
			// array so we only have to write code once.
			$this->_parent_coll = array($this->_parent_coll);
		}

		foreach ($this->_parent_coll as $model_parent)
		{
			// This is the first parent, which should be the last element in $parent, as
			// this was in reverse order from the API URI
			$supplied_parent = array_pop($parent);

			if (empty($supplied_parent) OR ! isset($supplied_parent['id']) OR $supplied_parent['name'] !== $model_parent['uri'])
			{
				throw new App_Exception('The parent resource was not supplied or was invalid');
			}

			// Create a MongoID from the ID
			if ( ! $supplied_parent['id'] instanceof MongoId)
			{
				$supplied_parent['id'] = new MongoId($supplied_parent['id']);
			}

			// Set the parent column with the ID of the parent model supplied 
			// in the API URI and the field supplied by the model.
			$this->set($model_parent['mongo'], $supplied_parent['id']);
		}

		return $this;
	}

	/**
	 * Default scaffolding for the GET API call. This loads a model (or 
	 * collection of models) based on the request URI. 
	 *
	 * Note that a subset of fields can be returned via the $_GET['fields'] 
	 * parameter and one can search for models using the $_GET['search'] 
	 * parameter.
	 *
	 *
	 * ## Searching using the API
	 *
	 * Externally, searches are made using the 'search' GET parameter. An 
	 * example is as follows:
	 *
	 * /api.json/v1/sites/?search=url.dom:example.com,name:Example+Site
	 *
	 * Note that these are NOT full text searches: they are exact text searches 
	 * only.
	 *
	 *
	 * ## Retrieving a subset of fields using the API
	 *
	 * To retrieve a subset of fields use the 'fields' query parameter as shown:
	 * 
	 * /api.json/v1/sites/?fields=_id,name
	 *
	 * This will return only the _id and name field. This can be matched with 
	 * searching.
	 *
	 *
	 * @param  array  An array of modifiers to the API call, such as 'search' to
	 *                search for resources or 'fields' to load certain fields
	 * @return array  array of content/metadata for API response
	 */
	public function API_Get($params = array())
	{

		// Run the helper method to determine which fields to pass as return 
		// values to MongoDB.
		$fields = $this->_initialise_subsets(Arr::get($params, 'fields'));

		if (isset($params['search']))
		{
			$this->_initialise_search($params['search']);
		}

		if ($this->get('_id') !== NULL)
		{
			// If the ID is set we only want 1 result.
			$this->load($fields);

			if ( ! $this->loaded())
				throw new App_API_Exception("We could not load the requested resource. Please check the request parameters to ensure the resource exists or ensure you are authorised to access the resource.", NULL, 404);

			return array(
				'content' => $this->original(),
				'metadata' => $this->metadata()
			);
		}

		// No ID is set so we return an array of results, even if there is only 
		// one result
		$cursor = $this->find($fields)->limit(20);

		$return = array(
			'content' => array(),
			'metadata' => $this->metadata() + array(
				'results' => $cursor->count()
			)
		);

		foreach($cursor as $item)
		{
			$return['content'][] = $item->original();
		}

		return $return;
	}

	/**
	 * Returns a $fields array instructing MongoDB which fields to return from 
	 * the database query.
	 *
	 * With null or an empty array passed to this method we return all visible 
	 * fields.
	 *
	 * @param string  String of fields such as "_id,name,desc"
	 * @return array  Array for the Mundo::find() method
	 */
	protected function _initialise_subsets($fields)
	{
		if ( ! $fields)
		{
			// We're getting all fields, so just return the hidden fields 
			// property which tells mongo to return all but these.
			return $this->_hidden_fields;
		}
		else
		{
			// Ensure we get only a subset of fields. To do this we need to loop 
			// through each field, add it to an array and ensure hidden fields 
			// aren't in there.

			// Get each individual field
			$fields = explode(',', $fields);

			if ( ! is_array($fields))
			{
				// If there's only one treat it like multiple anyway, to reduce 
				// code multiplication.
				$fields = array($fields);
			}

			// We need to ensure the fields are array keys and the values are 
			// '1' to show them through Mongo
			$matched_fields = array();
			foreach($fields as $value)
			{
				$matched_fields[$value] = 1;
			}

			// Add in our hidden fields, too, which will overwrite any explicit 
			// calls for them with a '0', instructing MongoDB to hide them
			$fields = array_merge($fields, $this->_hidden_fields);
		}

		// Ensure that we're not mixing including and excluding fields - MongoDB 
		// can't handle this. If there are mixes remove all hidden fields, as 
		// MongoDB is only going to return the ones we ask for
		if (in_array(1, $fields) && in_array(0, $fields))
		{
			foreach ($fields as $item => $value)
			{
				if ($value == 0)
					unset($fields[$item]);
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
	 * @return void
	 */
	protected function _initialise_search($terms)
	{
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
	}

	/**
	 * Default scaffolding for the PUT API call.
	 *
	 * Creates resources from given postdata and returns the newly-loaded model 
	 * data
	 *
	 * @return array array of content and metadata for the API response
	 */
	public function API_Put()
	{
		if (in_array('lmod', $this->_fields))
		{
			// Ensure last modified is set with the creation data
			$this->set('lmod', new MongoDate());
		}

		$this->set($_POST);
		$this->save($_POST);

		return array(
			'status' => 201,
			'content' => $this->original(),
			'metadata' => $this->metadata()
		);
	}

	/**
	 * Default scaffolding for the POST API call, used when updating an object
	 *
	 */
	public function API_Post()
	{
		$this->set('_id', new MongoId($_POST['_id']));
		$this->load();

		if ( ! $this->loaded())
		{
			throw new HTTP_Exception_404("Could not load the requested resource", NULL, 404);
		}

		// Ignore hidden fields. To amend these use raw mundo objects.
		$fields_to_ignore = array_keys($this->_hidden_fields);

		if (isset($this->_metadata['read_only']))
		{
			// Also ignore read only fields...
			$fields_to_ignore = array_merge($fields_to_ignore, $this->_metadata['read_only']);
		}

		foreach ($fields_to_ignore as $field)
		{
			if (isset($_POST[$field]))
			{
				unset($_POST[$field]);
			}
		}

		if (isset($_POST['_id']))
		{
			unset($_POST['_id']);
		}

		// Revision history
		if (in_array('hist', $this->_fields))
		{
			$deflated_data = App::$api->deflate_bindata($this->original('data'), $this->original('lmod'));
			$this->push('hist', $deflated_data);

			// Revision history always has a last modified field for revision 
			// history dates.
			$this->set('lmod', new MongoDate());
		}

		$this->set($_POST);
		$this->save();

		return array(
			'status' => 200,
			'content' => $this->original(),
			'metadata' => $this->metadata()
		);
	}

	/**
	 * Returns an array of metadata about this model. This metadata includes:
	 *  - A list of fields in the model
	 *  - Which fields are read only or binary
	 *  - Help or information about the model
	 *
	 * @return array
	 */
	abstract public function metadata();
}
