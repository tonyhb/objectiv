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

		$this->response->headers('Content-type', File::mime_by_ext($this->request->param('format')));

		// Store the name of the requested API class with the version number in a string for instatiation.
		$api = 'App_API_'.$this->request->param('version');

		if( ! Kohana::find_file('classes/app/api', $this->request->param('version')))
		{
			// Use the latest version
			$api = 'App_API_'.App::LATEST_API_VERSION;

			App::$api = new $api($this->request->param('format'));
			App::$api->set_format($this->request->param('format'));

			// Throw an error with the new API
			throw new App_API_Exception("The requested API version '{$this->request->param('version')}' does not exist.", null, 400);
		}

		// Instantiate the API in its static variable - this allows us to use the same 
		// API version as the one requested to throw error messages in index.php
		App::$api = new $api($this->request->param('format'));

		if ( ! App_Auth::authenticate())
			throw new App_API_Exception("You must authenticate before making API requests", NULL, 401);
	}


} // END class Controller_API
