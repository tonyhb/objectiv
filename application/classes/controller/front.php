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

			$this->template->styles = array('assets/css/front.css' => 'all');
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
	 * @param  $id   string  subdomain passed when accessing a site that doesn't exist.
	 * @return void
	 */
	public function action_sign_up()
	{
		if ($data = $this->request->post())
		{
			// Try creating account/useer/site. 
		}

		$this->template->body = View::factory('front/signup')
			->set('data', $this->request->post());
	}

} // END class Controller_Front extends Controller_Template