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
		$this->response_data = $this->_model->api_get($this->request->query());
	}

	/**
	 * This creates a new resource inside this collection.
	 *
	 * For example, by posting to /sites this will create a new site.
	 */
	public function action_post()
	{
		// Bootstrap.php will send JSON data as a request payload (into 
		// the php://input wrapper), which is visible in the request body.
		//
		// Check for a standard POST first.
		if ( ! $data = $this->request->post())
		{
			$data = $this->request->body();
			if ( ! $data = json_decode($data))
			{
				throw new Exception("Incorrect data supplied", 402);
			}
		}

		$this->response_data = $this->_model->api_post($data);
	}
}
