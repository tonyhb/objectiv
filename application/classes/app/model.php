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
}
