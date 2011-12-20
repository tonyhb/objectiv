<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This handles authentication and authorisation methods for the app, including
 * for the admin panel and the API.
 *
 * Note that the currently authorised user is saved in the App class, not this
 * one.
 *
 *
 * This will handle OAuth 2.0 logic.
 *
 * @package App
 * @subpackage Auth
 * @author Tony Holdstock-Brown
 **/
class App_Auth
{
	/**
	 * This is the cookie secret used to generate a HMAC key for each cookie
	 *
	 * @var string
	 */
	const COOKIE_KEY = ")WSNs9DzKD*dJ]n1]+Jy\JOwDBG)G*c/D|T_;Ep!@XwD7.#P$\=}O',=!E1z;SU<vP|@tiTY%@j)hv'@6";

	/**
	 * This method handles the authentication of the current user.
	 *
	 * The method accepts a single argument $options which should be in
	 * key => value form of the authentication model desired.
	 *
	 * Fore example, for email and password authentication $options
	 * should be like so:
	 * 
	 *   array('email' => 'foo', 'password' => 'bar');
	 * 
	 * This will eventually handle authorisation from OAuth tokens and
	 * hashes as well as passwords, but for the time being it is a simple
	 * email/password type check.
	 *
	 * @param  array  Array of authentication arguments
	 * @return bool   The status of authentication
	 */
	public static function authenticate($options = NULL)
	{
		if ( ! $options)
		{
			$options = array();
		}

		// Remove any empty values from the array
		$options = array_filter($options);

		// Get our authentication option keys for method testing
		$option_keys = array_keys($options);

		switch($option_keys)
		{
			case array('email', 'password'):

				// This is a email/password authentication check
				$user = Mundo::factory('users')
					->set('email', $options['email'])
					->load();


				if ( ! $user->loaded())
					return FALSE;

				$hash = Model_Users::hash($options['password'], substr($user->original('pw'), 0, 21));

				if ($hash != $user->original('pw'))
				{
					return FALSE;
				}

				// Set our app user
				App::$user = $user;

				break;

			default:
				// Try authenticating from cookie
				if ($cookie = Cookie::get("auth"))
				{
					// Split the cookie into its comprised parts
					list($user_id, $expiration, $hmac) = explode('|', $cookie);

					if ($expiration < time())
					{
						// This could have been tampered with; ensure this cookie gets deleted!
						Cookie::delete("auth");

						// They aren't authorised
						return FALSE;
					}

					// Generate the cookie key
					$key = hash_hmac('sha224', $expiration.$user_id, self::COOKIE_KEY);

					// Generate the hash for cookie authentication
					$hash = hash_hmac('sha224', $expiration.$user_id, $key);

					if ($hash != $hmac)
						return FALSE;

					$user = Mundo::factory('users')
						->set('_id', new MongoId($user_id))
						->load();

					if ($user->loaded())
					{
						App::$user = $user;

						return TRUE;
					}
				}

				// Either necessary data was missing from our authentication method or the parameters were wrong
				return FALSE;
		}

		// If we got here everything is dandy.
		return TRUE;
	}

	/**
	 * This method ensures a user has sufficient privileges to perform
	 * an action.
	 *
	 * @var array  Array of roles to check for. Note that if one of the
	 *             roles is found in the user tested this method will
	 *             return TRUE.
	 * @var array  array('site' => ..., 'user' => ...).
	 *             Used to override App::$site and App::$user defaults.
	 *             The values should be Mundo objects
	 * @return bool
	 */
	public static function authorise_user($roles, $parameters = array())
	{
		// If 'site' or 'user' isn't passed add our defaults
		$parameters += array('site' => App::$site, 'user' => App::$user);

		if ( ! Mundo::instance_of($parameters['user'], 'Mundo_Object'))
		{
			// Ensure that the user and site are loaded from the database 
			throw new App_Exception("The user passed to authroise should be a Mundo object, but a :class was passed", array(':class' => get_class($parameters['user'])));
		}

		if (Mundo::instance_of($parameters['site'], 'Mundo_Object'))
		{
			// We only need the ID of the site. Use the original() method to reduce memory usage.
			$parameters['site'] = $parameters['site']->original('_id');
		}

		foreach($parameters['user']->original('sites') as $site)
		{
			// If this isn't the site we're authorising skip it
			if($site['id'] != $parameters['site'])
			{
				// Unset before skipping
				$site = NULL;
				continue;
			}

			// We've found the site we are authrosising for, skip looping
			break;
		}

		// If we don't have a $site we couldn't find authorisation/roles for it, throw error
		if ( ! $site)
			return FALSE;

		while($roles)
		{
			// Get a role we're testing from our array
			$role = array_shift($roles);

			if (in_array($role, $site['roles']))
			{
				// The user has the requested role, everything is OK.
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * Sets a cookie for the admin panel/cookie based authentication
	 *
	 * This uses theory from the following blog post:
	 * http://raza.narfum.org/post/1/user-authentication-with-a-secure-cookie-protocol-in-php/
	 *
	 * @return void
	 */
	public static function set_cookie($user_id, $expires = 3600)
	{
		// Work out expiration timestamp
		$expiration = time() + $expires;

		// Generate the cookie key
		$key = hash_hmac('sha224', $expiration.$user_id, self::COOKIE_KEY);

		// Generate the hash for cookie authentication
		$hash = hash_hmac('sha224', $expiration.$user_id, $key);

		// Set the cookie
		Cookie::set("auth", $user_id."|".$expiration."|".$hash, $expires);
	}

} // END class App_Auth
