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
		$this->output = "GET";
	}

	/**
	 * Creates a new account with postdata
	 *
	 * @return mixed  json or xml with creation status
	 * @author Tony Holdstock-Brown
	 **/
	public function action_post()
	{
		throw new App_API_exception("Please pass all necessary details", NULL, 400);

		// This is an internal-only API; deny access if request is external
		if ($this->request->is_external())
		{
			// HTTP 405 Method Not Allowed
			$this->response->status(405);

			// Only allow viewing and updating account information
			$this->response->headers('Allow', 'GET, POST');

			return;
		}

		// Create an account model
		$account = Mundo::factory('account');

		// Attempt to register
		if ($account->register($this->request->post()))
		{
			$this->response->status(201);
		}
		else
		{
			$this->response->status(400);
		}
	}

} // END class API_1_Accounts extends API_1_Base
