<?php defined('SYSPATH') or die('No direct script access.');

/**
 * This is the dashboard for a site
 */
class Controller_Admin_Dashboard extends Controller_Admin
{

	public function action_index()
	{
		$this->template->body = View::factory('admin/dashboard');
	}

}
