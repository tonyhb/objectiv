<?php defined('SYSPATH') or die('No direct script access.');

// -- Environment setup --------------------------------------------------------

// Load the core Kohana class
require SYSPATH.'classes/kohana/core'.EXT;

if (is_file(APPPATH.'classes/kohana'.EXT))
{
	// Application extends the core
	require APPPATH.'classes/kohana'.EXT;
}
else
{
	// Load empty core extension
	require SYSPATH.'classes/kohana'.EXT;
}

/**
 * Set the default time zone.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/timezones
 */
date_default_timezone_set('Europe/London');

/**
 * Set the default locale.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/setlocale
 */
setlocale(LC_ALL, 'en_GB.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @see  http://kohanaframework.org/guide/using.autoloading
 * @see  http://php.net/spl_autoload_register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @see  http://php.net/spl_autoload_call
 * @see  http://php.net/manual/var.configuration.php#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

// -- Configuration and initialization -----------------------------------------

/**
 * Set the default language
 */
I18n::lang('en-gb');

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
 */
if (isset($_SERVER['KOHANA_ENV']))
{
	Kohana::$environment = constant('Kohana::'.strtoupper($_SERVER['KOHANA_ENV']));
}

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 */
Kohana::init(array(
	'base_url'   => '/',
	'index_file' => '',
	'profile'    => FALSE, //Kohana::$environment !== Kohana::PRODUCTION,
	'caching'    => Kohana::$environment === Kohana::PRODUCTION,
));

if (Kohana::$environment === Kohana::DEVELOPMENT)
{
	if (extension_loaded('xhprof'))
	{
		include_once '/usr/local/lib/php/xhprof_lib/utils/xhprof_lib.php';
		include_once '/usr/local/lib/php/xhprof_lib/utils/xhprof_runs.php';

		xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY + XHPROF_FLAGS_NO_BUILTINS);
	}
}

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Log_File(APPPATH.'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
	'hint'       => MODPATH.'hint',
	'uuid'       => MODPATH.'uuid',
	'mundo'      => MODPATH.'mundo',
	));

// HTTPS only cookies and set salt for admin cookies only; not general site cookies.
// Note that when loading the front of a site for a "random" visitor this will 
// be overwritten.
Cookie::$salt   = 'D^FKoHhBfbjksJ7L7p{aBcc3]ou#yB';
Cookie::$secure = TRUE;

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */

/**
 * This is the route for API calls.
 * 
 * We use lambda logic here because we can detect the format types using accept
 * headers before any controller logic, removing possible false defaults.
 * We can also map the http request method to the action without any logic
 * in the controller.
 **/
Route::set('api', function($uri)
	{
		// Compile the regex to ensure the API is called only with a valid URI
		$regex = Route::compile(
			'api(.<format>)/<version>(/<parameters>)',
			array(
				'version' => '(v[0-9]+)',
				'parameters' => '.*'
			)
		);

		if ( ! preg_match($regex, $uri, $matches))
			return FALSE;

		$matches = array_filter($matches) + array(
			'format'     => '',
			'parameters' => '',
			'controller' => 'api_'.$matches['version']
		);

		if ($matches['parameters'])
		{
			$matches['parameters'] = explode('/', $matches['parameters']); 

			// We're accessing an object; find out whether it's a single 
			// resource (odd number of API parameters) or a collection and route 
			// to the correct controller.
			$matches['controller'] .= (count($matches['parameters']) % 2 == 0) ? '_resource' : '_collection';

			// We also want to loop through the parameters to put each requested 
			// resource ID in an array with it's collection name
			$resources = array();

			for ($key = 0; $key < count($matches['parameters']); $key += 2)
			{
				if (isset($matches['parameters'][$key + 1]))
				{
					// This adds the requested ID to the collection name.
					$resources += array($matches['parameters'][$key] => $matches['parameters'][$key + 1]);
				}
				else
				{
					// We're requesting a resource with no ID (ie. a collection)
					$resources += array($matches['parameters'][$key] => NULL);
				}
			}
		}
		else
		{
			$resources = '';
			$action    = 'index';
		}

		// Add defaults to the returned parameters, and finish up.
		return array(
			'controller' => $matches['controller'],
			'action'     => strtolower($_SERVER['REQUEST_METHOD']),
			'format'     => $matches['format'],
			'version'    => $matches['version'],
			'resources'  => $resources,
		);
	});

Route::set('admin', function($uri)
	{
		// Check to see if we're accessing the admin section from a subdomain
		$url_segments = explode('.', $_SERVER['HTTP_HOST']);

		// Get our app URL from the config
		$app_url = Kohana::$config->load('app')->url;

		if ($url_segments[0] != 'admin' AND ($_SERVER['HTTP_HOST'] == $app_url AND substr($uri, 0, 5) != 'admin'))
		{
			return;
		}

		$uri_segments = explode("/", $uri);

		// Remove the any empties, normally caused by a trailing slash
		$uri_segments = array_filter($uri_segments);

		// Load an initial Mundo site object
		$site = App::model('sites');

		// Try and find out if we're accessing a specific site
		if (substr($_SERVER['HTTP_HOST'], 0, 5) == 'admin')
		{
			// We logged in to a specific site's admin panel from the admin subdomain
			$site_url = substr($_SERVER['HTTP_HOST'], 6);

			// Try loading the site from the URL
			$site->set('url.dom', $site_url)->load();

			if ( ! $site->loaded())
				return FALSE;

			App::$site = $site;
		}
		else if (isset($uri_segments[1]))
		{
			// Temporary hack for Backbone.js
			return array(
				'controller' => 'admin',
				'action' => 'index',
			);
			// End temporary hack!

			$site->set('_id', new MongoId($uri_segments[1]))->load();

			if ( ! $site->loaded())
				return FALSE;

			App::$site = $site;

			// Remove admin and the site id from uri segemnts
			$uri_segments = array_slice($uri_segments, 2);
		}
		else
		{
			// Show a list of sites
			return array(
				'controller' => 'admin',
				'action' => 'index',
			);
		}

		// Our controller is the first value
		$controller = count($uri_segments) ? array_shift($uri_segments) : 'dashboard'; 

		// Our action is now the first value or index by defalut
		$action = count($uri_segments) ? array_shift($uri_segments) : 'index';

		// Assign the rest of the URI segments as parameters
		$params = count($uri_segments) ? implode('/', $uri_segments) : NULL;

		return array(
			'controller' => 'admin_'.$controller,
			'action' => $action,
			'params' => $params,
		);

	});

Route::set('front', '(<action>(/<id>))')
	->defaults(array(
		'controller' => 'front',
		'action'     => 'index',
	));
