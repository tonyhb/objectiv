<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Manages database interaction for the site collection
 *
 * @packaged App
 * @author Tony Holdstock-Brown
 **/
class Model_Core_Sites extends Mundo_Object
{

	protected $_collection = 'site';

	protected $_fields = array(
		'id'   => '_id',
		'name' => 'name',

		// Site URL settings
		'url.canonical' => 'url.can', // Canonical
		'url.domain.$'  => 'url.dom.$', // A list of domain names and the CMS subdomain (foobar.cms.com)

		// Site settings 
		'option.language'     => 'opt.lang', // Default language
		'option.extension'    => 'opt.ext', // Is there a file extension to add to all pages (.html)?
		'option.preview'      => 'opt.dev.ok', // Is this in preview mode?
		'option.preview_pass' => 'opt.dev.pass', // What's the psasword for preview?

		// Robots.txt
		'robots',

		// Redirects
		'redirect.$,from'      => 'r.$.fr', // URL to redirect from
		'redirect.$,to'        => 'r.$.to', // URL to redirect to
		'redirect.$,permanent' => 'r.$.p', // Permanent? True for 301, False for 302 (for storage space)

		// Users
		'user.$.id'      => 'usr.$.id', 
		'user.$.name'    => 'usr.$.name',
		'user.$.roles.$' => 'usr.$.roles.$',

		// Account
		'account' => 'acct',
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

	protected $_read_only_fields = array(
		'_id',
		'acct', // Sites cannot be moved between accounts through the API.
	);

	public function metadata()
	{
		$metadata = parent::metadata();

		return array_merge($metadata, array(
			'children' => array(
				'pages',
				'objects',
				'themes'
			),
		));
	}

}
