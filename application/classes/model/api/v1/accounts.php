<?php defined('SYSPATH') OR die('No direct script access');

class Model_API_V1_Accounts extends Model_Core_Accounts
{
	use Model_API_V1; // Use our V1 API methods whilst still extending our main core models

	/**
	 * Fields that are not overwriteable through the public API.
	 * These must be manipulated through the admin panel in the site, if we 
	 * provide the option.
	 *
	 * @var array
	 */
	protected $_read_only_fields = array(
		'_id',
		'usr'
	);

	/**
	 * Fields that are not visible to the public through our API
	 *
	 * @var array
	 */
	protected $_private_fields = array();

	/**
	 * Resources accessible using this account as a parent.
	 *
	 * @var array
	 */
	protected $_child_resources = array('users');

	/**
	 * Binary-encoded fields
	 *
	 * @var array
	 */
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

}