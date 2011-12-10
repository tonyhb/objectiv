<?php defined('SYSPATH') or die('No direct script access.');

/**
 * This routes API calls to the App API class.
 *
 **/
class Controller_API extends Controller
{

	public function before()
	{
		if ( ! isset($_SERVER['HTTPS']))
		{
			// Ensure we always use HTTPS
			$this->request->redirect('https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		}

		// Set salt for admin cookies
		Cookie::$salt = 'D^FKoHhBfbjksJ7L7p{aBcc3]ou#yB';

		// Ensure our cookies are set on only secure connections
		Cookie::$secure = TRUE;

		// Store the name of the requested API class with the version number in a string for instatiation.
		$api = 'App_API_'.$this->request->param('version');

		if( ! Kohana::find_file('classes/app/api', $this->request->param('version')))
		{
			// The requested version doesn't exist. Load the latest version of the API
			$api = 'App_API_'.App::LATEST_API_VERSION;

			App::$api = new $api($this->request->param('format'));

			// Ensure we set the requested response format for this error.
			App::$api->set_format($this->request->param('format'));

			// Throw an error with the new API
			throw new App_API_Exception("The requested API version '{$this->request->param('version')}' does not exist.", null, 400);
		}

		// Instantiate the API in its static variable - this allows us to use the same 
		// API version as the one requested to throw error messages in index.php
		App::$api = new $api($this->request->param('format'));

		$this->response->headers('Content-type', File::mime_by_ext($this->request->param('format')));

		// @todo: Run some authentication methods here.
		if ( ! App_Auth::authenticate())
		{
			throw new App_API_Exception("You must authenticate before making API requests", NULL, 401);
		}
	}

	public function action_index()
	{
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

} // END class Controller_API
