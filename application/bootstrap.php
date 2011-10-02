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
setlocale(LC_ALL, 'en_US.utf-8');

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
I18n::lang('en-uk');

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
		// Compile the regex
		$regex = Route::compile('api(.<format>)/<version>(/<object>(/<id>))');

		if ( ! preg_match($regex, $uri, $matches))
			return FALSE;

		// If the format wasn't provided use format from Accept headers or JSON as default
		if (empty($matches['format']))
		{
			$matches['format'] = (strpos($_SERVER['HTTP_ACCEPT'], 'application/xml')) ? 'xml' : 'json';
		}

		// Add other defaults to the route
		$matches += array(
			'version' => '1',
			'object' => 'accounts',
			'id' => ''
		);

		if ($matches['object'] == 'base')
		{
			// Ensure no-one can access the base class
			unset($matches['object']);
		}

		return array(
			'directory' => 'api/'.$matches['version'],
			'controller' => $matches['object'],
			'action' => $_SERVER['REQUEST_METHOD'],
			'id' => $matches['id'],
			'format' => $matches['format'],
			'version' => $matches['version'],
		);
	},
	'api(.<format>)/<version>(/<object>(/<id>))');

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

		return array(
			'controller' => 'admin',
			'action' => 'index',
		);

	});

Route::set('front', '(<action>(/<id>))')
	->defaults(array(
		'controller' => 'front',
		'action'     => 'index',
	));
