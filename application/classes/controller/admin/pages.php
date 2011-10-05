<?php defined('SYSPATH') or die('No direct script access.');

/**
 * This class manages pages in the CMS
 *
 * @package App
 **/
class Controller_Admin_Pages extends Controller_Admin
{

	public function action_index()
	{
		// List all pages...
		$this->template->body = View::factory("admin/list_pages");
	}

	public function action_new()
	{
		if ( ! App_Auth::authorise_user(array('np', 'admin')))
		{
			// If a user doesn't have new page or admin privileges, redirect to the admin dashboard
			$this->request->redirect($this->template->base);
		}

		if ($this->request->post())
		{
			echo Debug::vars($this->request->post());
		}
		$this->template->body = View::factory("admin/new_page");
	}

} // END class Controller_Admin_Pages extends Controller_Admin
