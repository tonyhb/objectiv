<?php defined('SYSPATH') or die('No direct script access.');

/**
 * This is the base class for data in the app.
 *
 * An object is defined as a record of data in the CMS which isn't publicly
 * viewable from a URI. This is pretty much anything which isn't a page.
 *
 * This allows people to create advanced models, such as job offers, and add
 * metadata to objects without showing them as separate pages.
 *
 * All objects store revision histories of modifications on the 'data' field.
 *
 * @package App
 * @author Tony Holdstock-Brown
 **/
class Model_Objects extends App_Model
{
	protected $_collection = 'object';

	/** 
	 * Allow overrides in the data field only
	 *
	 * @var array
	 */
	protected $_schemaless = array(
		'data',
		'hist',
	);

	protected $_parent_coll = array(
		'uri' => 'sites',
		'mongo' => 'site'
	);

	protected $_fields = array(
		'_id',
		'mod', // Last modified
		'type', // Object type
		'name', // Object name
		'data', // Object data
		'hist', // Data history
		'site', // Site
	);

	protected $_rules = array(
		'type' => array(
			array('not_empty'),
			array('regex', array(':value', '#^[\w\s]+$#')),
		),
		'name' => array(
			array('not_empty'),
		),
		'data' => array(
			array('not_empty'),
		),
		'site' => array(
			array('not_empty'),
			array('Mundo::instance_of', array(':value', 'MongoId'))
		),
	);

	protected $_metadata = array(
		'read_only' => array(
			'_id',
			'hist',
			'site'
		),
		'binary' => array('hist')
	);

} // END class Model_Object
