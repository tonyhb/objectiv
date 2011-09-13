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
	 * This method takes postdata received from the sign up form and
	 * creates the necessary account, user and initial site.
	 *
	 *   !! Note that this method is for INTERNAL USE ONLY.
	 *
	 * @return mixed  json or xml with creation status
	 **/
	public function action_post()
	{
		// This is an internal-only API; deny access if request is external
		if ($this->request->is_external())
		{
			// HTTP 405 Method Not Allowed
			$this->response->status(405);

			// Only allow viewing and updating account information
			$this->response->headers('Allow', 'GET, PUT');

			return;
		}

		/**
		 * @todo Take only the accepted keys from post
		 */

		// Create an account model
		$account = Mundo::factory('account', $this->request->post());

		$account->create();

		// Attempt to register
		if ($account->_id instanceof MongoID)
		{
			// 201 Created
			$this->response->status(201);
		}
		else
		{
			// @todo Was this a server error or validation error? Return the correct status...
			$this->response->status(500);
		}
	}

	/**
	 * Change the specified account object, given the authorisation to
	 * do so.
	 */
	public function action_put($id)
	{
	}

	/**
	 * Called when a user wishes to delete their account.
	 *
	 * Accessible only through the admin panel: this is not an external
	 * method
	 */
	public function action_delete($id)
	{
		// This method is for internal use only.§
		if ($this->request->is_external())
		{
			$this->response->status(405);

			$this->response->headers('Allow', 'GET, PUT');

			return;
		}
	}

} // END class API_1_Accounts extends API_1_Base
