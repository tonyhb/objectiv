<?php defined('SYSPATH') OR die('No direct script access');

class Model_API_V1_Accounts extends Model_Core_Accounts implements Model_API_V1
{

	protected $_read_only_fields = array(
		'_id',
		'usr'
	);

	public function metadata()
	{
		$metadata = parent::metadata();

		return array_merge($metadata, array(
			'children' => array(
				'users'
			)
		));
	}

	public function API_Get($params = array())
	{
		$this->set('usr', array(array('id' => App::$user->get('_id'))));
	}

}
