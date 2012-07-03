<?php defined('SYSPATH') OR die('No direct script access');

class Model_API_V1_Accounts extends Model_Core_Accounts
{
	use Model_API_V1; // Use our V1 API methods whilst still extending our main core models

	protected $_read_only_fields = array(
		'_id',
		'usr'
	);

	/**
	 * Fields that are not visible to the public through our API
	 *
	 * @param array
	 */
	protected $_private_fields = array();

	protected $_child_resources = array('users');

	protected $_binary_fields = array();

	/**
	 * Initialises the model
	 *
	 * @see Model_API_V1::init()
	 * @return $this
	 */
	public function init($resource_chain = array())
	{
		// Ensure our user is allowed to access this account
		$this->push('usr', array('id' => App::$user->get('_id')));

		return $this;
	}

	/**
	 * Returns metadata regarding the 'Accounts' collection or a loaded account 
	 * resource
	 *
	 * @see Model_API_V1::metadata()
	 * @return array
	 */
	/*
	public function metadata()
	{
		$metadata = parent::metadata();

		return array_merge($metadata, array(
			'children' => array(
				'users'
			)
		));
	}
	 */

}
