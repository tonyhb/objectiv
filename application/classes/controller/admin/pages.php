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

} // END class Controller_Admin_Pages extends Controller_Admin
