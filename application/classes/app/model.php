<?php defined('SYSPATH') or die('No direct script access.');

class App_Model extends Mundo_Object
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
	 * Metadata returned with an API request
	 *
	 * @var array
	 */
	protected $_metadata = array();

	/**
	 * Used in API requests to ensure that models are only loaded according to 
	 * the parent specified in the URI
	 *
	 */
	public function set_parent($parent = array())
	{
		if ($this->_parent_coll === NULL)
			return;

		$parent = array_filter($parent);

		if (empty($parent) OR ! isset($parent['id']) OR $parent['name'] !== $this->_parent_coll['uri'])
		{
			throw new App_Exception('The parent resource was not supplied or was invalid');
		}

		if ( ! $parent['id'] instanceof MongoId)
		{
			$parent['id'] = new MongoId($parent['id']);
		}

		$this->set($this->_parent_coll['mongo'], $parent['id']);
	}

	/**
	 * Default scaffolding for the GET API call
	 *
	 * @param  array  An array of modifiers to the API call, such as 'search' to
	 *                search for resources or 'fields' to load certain fields
	 * @return array  array of content/metadata for API response
	 */
	public function API_Get($params = array())
	{
		if ( ! isset($params['fields']))
		{
			$params['fields'] = $this->_hidden_fields;
		}
		else
		{
			// Ensure we get only a subset of fields
			$params['fields'] = explode(',', $params['fields']);

			if ( ! is_array($params['fields']))
			{
				$params['fields'] = array($params['fields']);
			}

			// We need to ensure the fields are array keys and the values are 
			// '1' to show them through Mongo
			$fields = array();
			foreach($params['fields'] as $value)
			{
				$fields[$value] = 1;
			}

			// Ensure that by explicitly stating we want hidden fields they 
			// don't get them.
			$params['fields'] = array_merge($fields, $this->_hidden_fields);
		}

		// Ensure that we're not mixing including and excluding fields
		if (in_array(1, $params['fields']) && in_array(0, $params['fields']))
		{
			foreach ($params['fields'] as $field => $value)
			{
				if ($value == 0)
				{
					unset($params['fields'][$field]);
				}
			}
		}

		if (isset($params['search']))
		{
			// Searching is done in the format of 'field:value,field:value'
			// IE. a comma splits search terms

			$searched_fields = explode(',', $params['search']);

			foreach ($searched_fields as $item)
			{
				list($field, $term) = explode(':', $item);
				$this->set($field, $term);
			}
		}

		if ($this->get('_id') !== NULL)
		{
			// If the ID is set we only want 1 result.
			$this->load($params['fields']);

			if ( ! $this->loaded())
			{
				throw new App_API_Exception("We could not load the requested resource. Please check the request parameters to ensure the resource exists or ensure you are authorised to access the resource.", NULL, 404);
			}

			return array(
				'content' => $this->original(),
				'metadata' => $this->_metadata
			);
		}

		// No ID is set so we return an array of results, even if there is only 
		// one result
		$cursor = $this->find($params['fields'])->limit(20);

		$return = array(
			'content' => array(),
			'metadata' => $this->_metadata + array(
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
	 * Default scaffolding for the PUT API call.
	 *
	 * Creates resources from given postdata and returns the newly-loaded model 
	 * data
	 *
	 * @return array array of content and metadata for the API response
	 */
	public function API_Put()
	{
		$this->set($_POST);
		$this->save($_POST);

		return array(
			'status' => 201,
			'content' => $this->original(),
			'metadata' => $this->_metadata
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

		if (isset($this->_metadata['read_only']))
		{
			foreach ($this->_metadata['read_only'] as $field)
			{
				if (isset($_POST[$field]))
				{
					unset($_POST[$field]);
				}
			}
		}

		if (isset($_POST['_id']))
		{
			unset($_POST['_id']);
		}

		// Revision history
		if (in_array('hist', $this->_fields))
		{
			$compressed = gzdeflate($this->original('data'), 7);
			$this->push('hist', array(new MongoDate(), new MongoBinData($compressed)));
		}

		$this->set($_POST);
		$this->save();

		return array(
			'status' => 200,
			'content' => $this->original(),
			'metadata' => $this->_metadata
		);
	}
}
