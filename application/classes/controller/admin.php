<?php defined('SYSPATH') or die('No direct script access.');

/**
 * This is the base controller for the admin interface.
 *
 * @package CMS
 * @subpackage Admin
 **/
class Controller_Admin extends Controller_Template
{
	public $template = 'templates/admin';

	public function before()
	{
		if (Request::$initial->is_ajax())
		{
			// Ensure that the template isn't rendered with asynchronous requests
			$this->auto_render = FALSE;
		}

		if ( ! isset($_SERVER['HTTPS']))
		{
			// Ensure we always use HTTPS
			$this->request->redirect('https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		}

		// Call the standard template controller's before method
		parent::before();

		// Set salt for admin cookies
		Cookie::$salt = 'D^FKoHhBfbjksJ7L7p{aBcc3]ou#yB';

		// Ensure our cookies are set on only secure connections
		Cookie::$secure = TRUE;

		// Template stuff
		if ($this->auto_render)
		{
			// Initialise empty variables for the render process
			$this->template->title =
			$this->template->body = '';

			$this->template->styles = array('assets/css/admin.css' => 'all');
			$this->template->meta = array();

			if (App::$site)
			{
				$this->template->base = (substr($_SERVER['HTTP_HOST'], 0, 5) == 'admin') ? '' : '/admin/'.App::$site->get('_id');
			}
			else
			{
				$this->template->base = (substr($_SERVER['HTTP_HOST'], 0, 5) == 'admin') ? '' : '/admin/';
			}
		}

		// Check authentication and authorisation
		if ( ! App_Auth::authenticate() OR (App::$site AND ! App_Auth::authorise_user(array('login', 'admin'))))
		{
			if ( ! App_Auth::authenticate($this->request->post()))
			{
				$this->request->action('login');
				return;
			}
			else
			{
				// Create a new CSRF token for the user upon valid logins
				Cookie::set('csrf', UUID::v4());
			}
		}

		// Ensure the cookie's expiry is set from this hit
		App_Auth::set_cookie( (string) App::$user->_id);
	}

	/**
	 * This method handles login logic for the admin panel.
	 *
	 * If a user is already logged in we call the index function by default.
	 *
	 * @return void
	 */
	public function action_login()
	{
		if (App_Auth::authenticate($this->request->post()) AND (App::$site AND App_Auth::authorise_user(array('login', 'admin'))))
		{
			// Call the default action
			$this->action_index();

			// Halt processing
			return;
		}

		if (App::$site)
		{
			// Show a noindex tag for subdomains on people's sites
			$this->template->meta = array('robots' => 'noindex');
		}

		$this->template->body = View::factory('admin/login');
	}

	/**
	 * This method handles admin panel routing. The admin routing logic in
	 * the bootstrap uses lambda logic to account for both subdomains and
	 * $cms.com/admin access routes.
	 *
	 * This is called after before() but before any administration logic.
	 * For this reason, this method handles authentication, authorisation
	 * and site loading.
	 *
	 * @return void
	 */
	public function action_index()
	{
		// Show the list of sites
		$this->template->body = View::factory('admin/site_list');
	}

	/**
	 * This is executed after all main controller logic
	 *
	 */
	public function after()
	{
		if (is_object($this->template->body))
		{
			// Ensure that if we use the $base variable in the main content it still works
			$this->template->body->base = $this->template->base;
		}

		// Call the standard template rendering process
		parent::after();
	}

} // END class controller_admin extends controller
