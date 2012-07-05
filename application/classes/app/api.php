<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Helper class for API methods. 
 *
 * @package App
 * @subpackage API
 */
class App_API
{

	/**
	 * Instantiates and returns an instance of an API model.
	 *
	 * @var string      Class name of the model to instantiate
	 * @var string|int  API version to load
	 * @return mixed    Model
	 */
	public static function model($name, $version = NULL)
	{
		if ($version == NULL)
		{
			$version = self::$api_latest;
		}

		if (is_int($version) OR strpos($version, 'v') !== 0)
		{
			$version = 'v'.$version;
		}

		$model = 'Api_'.$version.'_'.$name;

		if ( ! Kohana::find_file('classes', str_replace('_', '/', 'Model_'.$model)))
		{
			throw new App_API_Exception("Could not find the model file ':model'", array(':model' => $name));
		}

		return Mundo::factory($model);
	}

	/**
	 * The latest version of the API
	 *
	 * @var string
	 */
	public static $api_latest = '1';

	/**
	 * Stores the available API versions
	 *
	 * @var array
	 */
	public static $api_versions = array('1');

}
