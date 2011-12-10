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
	 *
	 */
	public function set_parent($parent = array())
	{
		if ($this->_parent_coll === NULL)
			return;

		$parent = array_filter($parent);

		if (empty($parent) OR ! isset($parent['id']) OR $parent['name'] !== $this->_parent_coll['uri'])
		{
			throw new App_Exception('The parent object for "user" was not supplied or was invalid');
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
	 * @return array array of content/metadata for API response
	 */
	public function API_Get()
	{
		if ($this->get('_id') !== NULL)
		{
			$this->load($this->_hidden_fields);

			if ( ! $this->loaded())
			{
				throw new App_API_Exception("We could not load the requested resource. Please check the request parameters to ensure the resource exists or ensure you are authorised to access the resource.", NULL, 404);
			}

			return array(
				'content' => $this->get(),
				'metadata' => $this->_metadata
			);
		}

		$cursor = $this->find($this->_hidden_fields)->limit(20);

		$return = array(
			'content' => array(),
			'metadata' => $this->_metadata + array(
				'results' => $cursor->count()
			)
		);

		foreach($cursor as $item)
		{
			$return['content'][] = $item->get();
		}

		return $return;
	}
}