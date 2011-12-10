<?php defined('SYSPATH') or die('No direct script access.');

/**
 * This class manages pages in the CMS
 *
 * @package App
 **/
class Controller_Admin_Pages extends Controller_Admin
{

	/**
	 * List all pages in the current site
	 *
	 */
	public function action_index()
	{
		$this->template->body = View::factory("admin/pages/list");
	}

	/**
	 * Create a new page in the current site
	 *
	 */
	public function action_new()
	{
		if ( ! App_Auth::authorise_user(array('np', 'admin')))
		{
			// If a user doesn't have new page or admin privileges, redirect to the admin dashboard
			$this->request->redirect($this->template->base);
		}

		if ($this->request->post())
		{
			/**
			 * False API call
			 */
			$return = App::$API->post('page', $this->request->post());
			echo Debug::vars($this->request->post());
		}

		$this->template->body = View::factory("admin/pages/new");
	}

} // END class Controller_Admin_Pages extends Controller_Admin
