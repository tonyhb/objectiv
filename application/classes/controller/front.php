<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Loads the front-end of the CMS
 *
 * @packaged CMS
 * @author Tony Holdstock-Brown
 **/
class Controller_Front extends Controller_Template
{
	public $template = 'front/template';

	/**
	 * Initialises default template variables
	 *
	 * @return void
	 **/
	public function before()
	{
		// Call the template parent function
		parent::before();

		if ($this->auto_render)
		{
			// Initialise empty variables for the render process
			$this->template->title =
			$this->template->body = '';
		}
	}

	/**
	 * Load the home page
	 *
	 * @return void
	 **/
	public function action_index()
	{

	}

	/**
	 * Load the sign up page
	 *
	 * @return void
	 */
	public function action_sign_up()
	{
		$this->template->body = View::factory('front/signup');
	}

} // END class Controller_Front extends Controller_Template
