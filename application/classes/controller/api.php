<?php defined('SYSPATH') or die('No direct script access.');

/**
 * This routes API calls to the App API class.
 *
 **/
class Controller_API extends Controller
{

	public function before()
	{
		// Store the name of the requested API class with the version number in a string for instatiation.
		$api = 'App_API_'.$this->request->param('version');

		// Instantiate the API in its static variable - this allows us to use the same 
		// API version as the one requested to throw error messages in index.php
		App::$api = new $api;

		// Set the response format for this API call.
		App::$api->set_format($this->request->param('format'));

		// @todo: Run some authentication methods here.
	}

	public function action_index()
	{
		// The request method is used in the next few lines, so store it in a convenience variable.
		$api_method = $this->request->method();

		// Ensure the request method is one we expect and can handle.
		if ( ! in_array($api_method, array('PUT', 'POST', 'GET', 'DELETE')) OR ! $response = App::$api->call($api_method))
		{
			// We only handle PUT, POST, GET and DELETE methods
			throw new App_API_Exception("Accepted HTTP Methods are PUT, POST, GET and DELETE", NULL, 400);
		}

		// Call the API method specified in the request method
		$response = App::$api->call($api_method);

		echo Debug::vars($response);
	}

} // END class Controller_API
