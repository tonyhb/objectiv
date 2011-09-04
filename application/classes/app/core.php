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

} // END class App_Core
