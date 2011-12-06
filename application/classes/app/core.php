<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Contains the low-level functionality of the app.
 *
 * This file manages initialisation and site loading.
 *
 * @package App
 * @author Tony Holdstock-Brown
 **/
class App_Core
{

	const VERSION = "0.0.1";

	const LATEST_API_VERSION = 'v1';

	/**
	 * Stores the currently loaded site
	 *
	 * @var object
	 **/
	public static $site;

	/**
	 * Stores the currently loaded user
	 *
	 * @var object
	 **/
	public static $user;

	/**
	 * Stores the current language of the site
	 *
	 * @var object
	 **/
	public static $language;

	/**
	 * Stores the API instance used in the request
	 *
	 * @var App_API_vX
	 */
	public static $api;

	/**
	 * Attempts to register a user and account with the CMS.
	 *
	 * Note the use of Arr::get($array, 'key') instead of $array['key'] so
	 * we can properly validate the array with missing data instead of
	 * PHP throwing an ErrorException because of an invalid array key.
	 *
	 * This allows us to report back the correct status code (400) instead 
	 * of a 500 internal server error.
	 *
	 * @param   array  data from the registration form
	 * @return  mixed  Boolean TRUE if successful, array of validation errors otherwise.
	 * @throws  mixed  Database exceptions
	 */
	public static function register($data)
	{
		// Purge empty fields just in case
		$data = array_filter($data);

		$account = Mundo::factory('accounts', array(
			'contact' => Arr::get($data, 'contact_name'),
			'company.name' => Arr::get($data, 'company_name'),
		));

		$user = Mundo::factory('users', array(
			'name' => Arr::get($data, 'contact_name'),
			'email' => Arr::get($data, 'contact_email'),
			'pw' => Arr::get($data, 'password')
		));

		$site = Mundo::factory('sites', array(
			'name' => Arr::get($data, 'site_name'),

			// Default language
			'opt.lang' => 'en',
		));

		// Put the subdomain and optional URL into an array
		$domains = array(
			// Append the app URL to the subdomain for correct routing
			Arr::get($data, 'site_address').'.'.Kohana::$config->load('app')->url, 

			// Remove the www from any URL
			str_replace('www.', '', Arr::get($data, 'domain_name'))
		);

		// Empty the array to remove the NULL if domain_name was empty
		$domains = array_filter($domains);

		// Set the domains of the new site
		$site->set('url.dom', $domains);

		// Get the canonical - use the domain if it exists, or the subdomain
		$site->{'url.can'} = (Arr::get($data, 'domain_name') === NULL) ? "http://".$domains[0] : "http://".$data['domain_name'];

		// Validate all models before any creation begins
		$validate['user'] = $user->validate();
		$validate['account'] = $account->validate();
		$validate['site'] = $site->validate();

		$validation_errors = array();

		foreach ($validate as $model => $valid)
		{
			if ($valid->check() == FALSE)
			{
				if (empty($validation_errors))
				{
					$validation_errors = $valid->errors('');
				}
				else
				{
					$validation_errors += $valid->errors('');
				}
			}
		}

		// We need to check the site subdomain was passed beforehand, as we add the CMS url to it which automatically validates 'not_empty'
		if (Arr::get($data, 'site_address') === NULL)
		{
			$validation_errors['site_address'] = "Please supply a subdomain for the site";
		}

		if ( ! empty($validation_errors))
		{
			return $validation_errors;
		}

		// Hash the password
		$user->pw = Model_User::hash($user->pw);

		try
		{
			// Create the new account
			$account->create();

			// Set the user's account ID before creation
			$user->set('acct', $account->_id);

			// Create the user
			$user->create();

			// Add the user to the account
			$account->push('usr', array(
				'id' => $user->_id,
				'name' => $user->name
			));

			// Atomically update the account with the new user details
			$account->update();

			// Create the site.
			$site->set(array(
				'acct' => $account->_id,
				'usr' => array(
					array(
						'id' => $user->_id,
						'name' => $user->name,
						'roles' => array('admin'),
					),
				),
			));
			$site->create();

			// Update the user with our site roles
			$user->push('sites', array(
				'id' => $site->_id,
				'name' => $site->name,
				'roles' => array('admin'),
			));

			$user->update();
		}
		catch(Exception $e)
		{
			/**
			 * @todo Cleanup: delete created models OR 2PC for creating accounts?
			 */

			// Throw the error; the error message will be caught in the controller
			throw $e;
		}

		return TRUE;
	}
} // END class App_Core
