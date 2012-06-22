<?php defined('SYSPATH') or die('No direct script access');

class Controller_Api_V1 extends Controller_API
{

	const VERSION = '1';

	/**
	 * This action is called when a resource collection has not been supplied.
	 *
	 * Because no API method has really been called this method produces data 
	 * for discoverability, letting a user or client explore the API from our 
	 * repsonses
	 *
	 * @return void
	 */
	public function action_index()
	{
		echo 'discoverability';
		return;
		// The request method is used in the next few lines, so store it in a convenience variable.
		$api_method = $this->request->method();

		// Ensure the request method is one we expect and can handle.
		if ( ! in_array($api_method, array('PUT', 'POST', 'GET', 'DELETE')) OR ! $response = App::$api->call($api_method, $this->request->param('parameters')))
		{
			// We only handle PUT, POST, GET and DELETE methods
			throw new App_API_Exception("Accepted HTTP Methods are PUT, POST, GET and DELETE", NULL, 400);
		}

		$this->response->body($response);
	}

	/**
	 * This action is called when a user requests a resource collection, such as 
	 * `/api/v1/sites`.
	 *
	 */
	public function action_collection()
	{
		$model = implode('_', array_keys($this->request->param('resources')));

		try
		{
			$model = App_API::model($model);
		}
		catch (App_API_Exception $e)
		{
			// This model couldn't be found; we must be loading a custom 
			// resource collection from the Object model.

			// @TODO: Load custom resources from the Object model
			throw $e;
		}

		print_r(get_class($model));
	}

	public function action_resource()
	{
		echo "Resource";
		print_r($this->request->param());
	}
}
