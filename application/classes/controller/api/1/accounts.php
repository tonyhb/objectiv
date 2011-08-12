<?php defined('SYSPATH') or die('No direct script access.');

/**
 * API for account management
 *
 * @packaged App
 * @author Tony Holdstock-Brown
 **/
class Controller_API_1_Accounts extends Controller_API_1_Base
{

	public function action_get()
	{
	}

	/**
	 * Creates a new account with postdata
	 *
	 * @return mixed  json or xml with creation status
	 * @author Tony Holdstock-Brown
	 **/
	public function action_put()
	{
		// This is an internal-only API; deny access if request is external
		if ($this->request->is_external())
		{
			// HTTP 405 Method Not Allowed
			$this->response->status(405);

			// Only allow viewing and updating account information
			$this->response->headers('Allow', 'GET, POST');

			return;
		}

		// @TODO: Take postdata, try creating a model and return a 201/400 accordingly
	}

} // END class API_1_Accounts extends API_1_Base
