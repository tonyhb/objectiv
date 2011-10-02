<?php defined('SYSPATH') or die('No direct script access.');

/**
 * This is the base controller for the admin interface.
 *
 * @package CMS
 * @subpackage Admin
 **/
class Controller_Admin extends Controller_Template
{
	public $template = 'templates/html5';

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

		// Template stuff
		if ($this->auto_render)
		{
			// Initialise empty variables for the render process
			$this->template->title =
			$this->template->body = '';

			$this->template->styles = array('assets/css/admin.css' => 'all');
			$this->template->meta = array();
		}

		// Check authentication and authorisation
		if ( ! App_Auth::authenticate($this->request->post()) OR (App::$site AND ! App_Auth::authorise_user(array('login', 'admin'))))
		{
			$this->request->action('login');

			return;
		}

		// Ensure the cookie's expiry is set from this hit
		App_Auth::set_cookie( (string) App::$user->_id);
	}

	public function action_login()
	{
		if (App_Auth::authenticate($this->request->post()) AND (App::$site AND ! App_Auth::authorise_user(array('login', 'admin'))))
		{
			// Call the base action
			$this->action_index();
		}

		if (App::$site)
		{
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

} // END class controller_admin extends controller
