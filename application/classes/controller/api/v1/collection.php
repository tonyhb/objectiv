<?php defined('SYSPATH') or die('No direct script access');

/**
 * Handles interaction between users, models and views for all collections 
 * called from the V1 API.
 *
 * @category API
 * @subcategry API Version 1
 */
class Controller_API_V1_Collection extends Controller_API_V1
{

	/**
	 * Stores the API Mundo Model
	 *
	 * @var mixed  Model_API_V1_$Name
	 */
	protected $_model;

	public function action_get()
	{
		$this->data = $this->_model->api_get($this->request->query());
	}

}
