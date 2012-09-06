<?php defined('SYSPATH') or die('No direct script access.');

/**
 * This is the base controller for the admin interface.
 *
 * @package CMS
 * @subpackage Admin
 **/
class Controller_Admin extends Controller
{

	protected $_view;

	public function before()
	{
		if ( ! isset($_SERVER['HTTPS'])) {
			// Ensure we always use HTTPS
			$this->request->redirect('https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		}

		// Check for pre-existing authentication and authorisation
		if ( ! App_Auth::authenticate() OR (App::$site AND ! App_Auth::authorise_user(array('login', 'admin'))))
		{
			$this->request->action('login');
			return;
		}

		if ( ! Cookie::get('csrf'))
		{
			// Create a new CSRF token for the user upon valid logins and token 
			// refreshes. Do NOT check for the cookie's existence in CSRF 
			// checking because it doesn't prevent any clickjacking attacks. 
			// Instead look for the token in the request.
			Cookie::set('csrf', UUID::v4());
		}

		// Ensure the cookie's expiry is set from this hit
		App_Auth::set_cookie( (string) App::$user->_id);
	}

	/**
	 * This method handles login logic for the admin panel.
	 * If a user is already logged in we call the index function by default.
	 *
	 * @return void
	 */
	public function action_login()
	{
		if (App_Auth::authenticate($this->request->post()) AND (App::$site AND App_Auth::authorise_user(array('login', 'admin'))))
		{
			// Successful login; run the default action and quit
			$this->action_index();
			return;
		}

		$this->_view = View::factory('admin/login')->set(array(
			'title'  => 'Log in',
			'meta'   => array()
		));

		if (App::$site) {
			// Show a noindex tag for subdomains on people's sites
			$this->_view->meta = array('robots' => 'noindex');
		}

	}

	/**
	 *
	 * @return void
	 */
	public function action_index()
	{
		$sites = Mundo::Factory('api/v1/sites')
			->init()
			->API_Get();

		$this->_view = View::Factory('admin/template')->set(array(
			'meta' => array('csrf' => Cookie::get('csrf')),
			'sites' => $sites['content']
		));
	}

	/**
	 * This is executed after all main controller logic
	 *
	 */
	public function after()
	{
		if ( ! $this->_view) {
			$this->_view = View::Factory('admin/template');
		}

		$this->response->body($this->_view->render());
	}

} // END class controller_admin extends controller
