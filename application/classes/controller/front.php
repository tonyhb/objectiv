<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Loads the front-end of the CMS
 *
 * @packaged CMS
 * @author Tony Holdstock-Brown
 **/
class Controller_Front extends Controller_Template
{
	public $template = 'templates/html5';

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
		$this->template->body = '<h1>CMS Home</h1>';
	}

	/**
	 * Load the sign up page
	 *
	 * @param  $id   string  subdomain passed when accessing a site that doesn't exist.
	 * @return void
	 */
	public function action_sign_up()
	{
		$this->template->body = View::factory('front/signup');

		if ($data = $this->request->post())
		{
			try
			{
				// This isn't an API call, this is a register function.
				$registered = App::register($this->request->post());
			}
			catch(Exception $e)
			{
				// Set tbe error message to display in the next if block
				$registered = array($e->getMessage());
			}

			if ($registered === TRUE)
			{
				$this->request->redirect('signed_up');
			}
			else
			{
				$this->template->body->set('errors', $registered);
			}
		}

		$this->template->body->set('data', $this->request->post());
	}

	public function action_signed_up()
	{
		$this->template->body = "Thank you for signing up.";
	}

} // END class Controller_Front extends Controller_Template
