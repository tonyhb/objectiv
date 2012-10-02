<?php defined('SYSPATH') OR die('No direct script access');

class Model_API_V1_Sites_Themes extends Model_Core_Sites_Themes
{
	use Model_API_V1; // Use our V1 API methods whilst still extending our main core models

	protected $_private_fields = array();

	/**
	 * Resources accessible using this account as a parent.
	 *
	 * @var array
	 */
	protected $_child_resources = array('html', 'css', 'js'); // And LESS/SASS when supported

	/**
	 * Fields that are not overwriteable through the public API.
	 * These must be manipulated through the admin panel in the site, if we 
	 * provide the option.
	 *
	 * @var array
	 */
	protected $_read_only_fields = array(
		'_id',
		'lm'
	);

	/**
	 * Initialises the model.
	 *
	 * @see Model_API_V1::init()
	 * @return $this
	 */
	public function init($resource_chain = array())
	{
		$this->push('site', array('id' => $resource_chain['sites']));

		return $this;
	}


}
