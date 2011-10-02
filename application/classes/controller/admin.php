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

	/**
	 * This stores the controller name from the URI
	 *
	 * @var string
	 */
	protected $_controller = '';

	/**
	 * This stores the controller action from the URI
	 *
	 * @var string
	 */
	protected $_action = '';

	/**
	 * This stores any parameters passed to the action from the URI
	 *
	 * @var mixed
	 */
	protected $_params = NULL;

	public function before()
	{
		if ( ! isset($_SERVER['HTTPS']))
		{
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
		// Check authentication
		if ( ! App_Auth::authenticate($this->request->post()))
		{
			$this->template->body = View::factory('admin/login');

			// Go no further, amigo!
			return;
		}


		// Find out which site we're working on and authorise against it
		if ( ! $this->_detect_site())
			return;

		// Ensure the cookie's expiry is set from this hit
		App_Auth::set_cookie( (string) App::$user->_id);

		if ( ! App::$site)
		{
			// Show the list of sites
			$this->template->body = View::factory('admin/site_list');
			return;
		}

		// Try routing the request

		try
		{
			$controller = 'Controller_Admin_'.$this->_controller;
			$controller = new $controller($this->request, $this->response);

			$controller->{$this->_action}($this->_params);

			$this->auto_render = FALSE;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}

	/**
	 * This method checks the URL and URI to see which site we are editing.
	 * 
	 * The URL can be something like 'admin.site.com', indicating we are
	 * editing 'site.com', or the URI could be 'cms.com/admin/{site_id}'.
	 *
	 * The App::$site variable is set on discovery of a site.
	 *
	 * @return void
	 */
	protected function _detect_site()
	{
		// Find out what we're actually doing in the admin panel
		$uri_segments = explode("/", $this->request->uri());

		// Remove the any empties, normally caused by a trailing slash
		$uri_segments = array_filter($uri_segments);

		// Load an initial Mundo site object
		$site = Mundo::factory('site');

		// Try and find out if we're accessing a specific site
		if (substr($_SERVER['HTTP_HOST'], 0, 5) == 'admin')
		{
			// We logged in to a specific site's admin panel from the admin subdomain
			$site_url = substr($_SERVER['HTTP_HOST'], 6);

			// Try loading the site from the URL
			$site->set('url.dom', $site_url)->load();

			// Set our site variable
			App::$site = $site;

			if ( ! App_Auth::authorise_user(array('login', 'admin')))
			{
				// We used the admin subdomain for a site but the user doens't have privileges, show login
				$this->template->body = View::factory('admin/login');

				return FALSE;
			}

			// Ensure the admin panel is never indexed on site subdomains
			$this->template->meta = array('robots' => 'noindex');
		}
		else if (isset($uri_segments[1]))
		{
			$site->set('_id', new MongoId($uri_segments[1]))->load();

			// Set our site variable
			App::$site = $site;

			if ( ! App_Auth::authorise_user(array('login', 'admin')))
			{
				// We're accessig cms.com/admin but have attempted to load an unauthorised site - redirect home
				$this->request->redirect('/admin');

				return FALSE;
			}

			// Remove admin and the site id from uri segemnts
			$uri_segments = array_slice($uri_segments, 2);
		}

		// Our controller is the first value
		$this->_controller = count($uri_segments) ? array_shift($uri_segments) : 'Dashboard'; 

		// Our action is now the first value or index by defalut
		$this->_action = count($uri_segments) ? array_shift($uri_segments) : 'Index';

		// Assign the rest of the URI segments as parameters
		$this->_params = count($uri_segments) ? $uri_segments : NULL;

		return TRUE;
	}

} // END class controller_admin extends controller
