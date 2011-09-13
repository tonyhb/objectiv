<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Manages database interaction for the site collection
 *
 * @packaged App
 * @author Tony Holdstock-Brown
 **/
class Model_Site extends Mundo_Object
{

	protected $_collection = 'site';

	protected $_fields = array(
		'_id',
		'name',

		// Site URL settings
		'url.can', // Canonical
		'url.dom.$', // A list of domain names and the CMS subdomain (foobar.cms.com)

		// Site settings 
		'opt.lang', // Default language
		'opt.ext', // Is there a file extension to add to all pages (.html)?
		'opt.dev.ok', // Is this in preview mode?
		'opt.dev.pass', // What's the psasword for preview?

		// Site meta
		'meta.robots',

		// Redirects
		'r.$.fr', // URL to redirect from
		'r.$.to', // URL to redirect to
		'r.$.p', // Permanent? True for 301, False for 302 (for storage space)

		// Users
		'usr.$.id', 
		'usr.$.name',
		'usr.$.roles.$',

		// Account
		'acct',
	);

	protected $_rules = array(
		'name' => array(
			array('not_empty'),
		),
		'url.can' => array(
			array('not_empty'),
			array('url'),
		),
		'url.dom.$' => array(
		),
		'opt.lang' => array(
			array('not_empty'),
			array('alpha_dash'),
			// Confirms to BCP47?
		),
		'opt.ext' => array(
			array('alpha'),
		),
		'r.$.fr' => array(
			array('not_empty'),
		),
		'r.$.to' => array(
			array('not_empty'),
		),
		'usr.$.id' => array(
			array('Mundo::instance_of', array(':value', 'MongoId')),
		),
		'usr.$.name' => array(
			array('not_empty'),
			array('min_length', array(':value', 6)),
			array('regex', array(':value', '#^[\w\s-\.]+$#')),
		),
		'usr.$.roles' => array(
			array('alpha'),
		),
		'acct' => array(
			array('Mundo::instance_of', array(':value', 'MongoId')),
		),
	);

}
