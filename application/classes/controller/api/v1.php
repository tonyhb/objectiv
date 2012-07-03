<?php defined('SYSPATH') or die('No direct script access');

/**
 * The base class for V1 controllers
 *
 * @category API
 * @subcategry API Version 1
 */
class Controller_API_V1 extends Controller_API
{

	const VERSION = '1';

	/**
	 * The result of the API call to be encoded and returned to the user
	 *
	 * @var mixed
	 */
	protected $data;

	/**
	 * Initialises the requested resource model
	 *
	 * @return void
	 */
	public function before()
	{
		parent::before();

		if ($this->request->param('resources'))
		{
			$model_name = implode('_', array_keys($this->request->param('resources')));

			try
			{
				$this->_model = App_API::model($model_name);
			}
			catch (App_API_Exception $e)
			{
				// This model couldn't be found; we must be loading a custom 
				// resource collection from the Object model.

				// @TODO: Load custom resources from the Object model
				throw $e;
			}

			$this->_model->init($this->request->param('resources'));
		}
	}

	/**
	 * This action is called when a resource collection has not been supplied.
	 *
	 * Because no API method has really been called this method produces data 
	 * for discoverability, letting a user or client explore the API from our 
	 * repsonses
	 *
	 * @return void
	 */
	public function action_get()
	{
		echo 'discoverability';
	}

	public function after()
	{
		$output = View::factory('api/'.$this->request->param('format'))
			->set('data', $this->data)
			->render();

		$this->response->body($output);
	}

}
