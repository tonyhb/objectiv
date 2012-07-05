<?php defined('SYSPATH') or die('No direct script access.');

/**
 * This routes API calls to the App API class.
 *
 **/
class Controller_API extends Controller
{

	public function before()
	{
		// Ensure we always use HTTPS
		if ( ! isset($_SERVER['HTTPS']))
		{
			$this->request->redirect('https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		}

		$this->response->headers('Content-type', File::mime_by_ext($this->request->param('format')));

		if ( ! App_Auth::authenticate())
			throw new App_API_Exception("You must authenticate before making API requests", NULL, 401);
	}


} // END class Controller_API
