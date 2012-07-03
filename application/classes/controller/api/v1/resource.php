<?php defined('SYSPATH') or die('No direct script access');

/**
 * Handles interaction between users, models and views for all collections 
 * called from the V1 API.
 *
 * @category API
 * @subcategry API Version 1
 */
class Controller_API_V1_Resource extends Controller_API_V1
{

	/**
	 * Stores the API Mundo Model
	 *
	 * @var mixed  Model_API_V1_$Name
	 */
	protected $_model;

	public function before()
	{
		parent::before();

		$resources   = $this->request->param('resources');

		// Take the last resource passed and find its ID
		$resource_id = array_slice($resources, count($resources) - 1);
		$resource_id = array_pop($resource_id);

		$this->_model->set('_id', new MongoId($resource_id));
	}

	public function action_get()
	{
		echo json_encode($this->_model->api_get($this->request->query()));
	}

}
