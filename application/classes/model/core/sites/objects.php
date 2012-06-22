<?php defined('SYSPATH') or die('No direct script access.');

/**
 * This is the base class for data in the app.
 *
 * An object is defined as a record of data in the CMS which isn't publicly
 * viewable from a URI. This is pretty much anything which isn't a page.
 *
 * All objects store revision histories of modifications on the 'data' field.
 *
 * This allows people to create advanced models, such as job offers, and add
 * metadata to objects without showing them as separate pages. All default 
 * objects, such as CSS and layouts, extend this.
 *
 * Default objects have a 'cstm' field value of FALSE, whilst custom objects 
 * have a value of TRUE. This allows us to show only custom objects in an API 
 * call to objects by setting this field in the call.
 *
 *
 * @package App
 * @author Tony Holdstock-Brown
 **/
class Model_Core_Sites_Objects extends App_Model
{
	protected $_collection = 'object';

	protected $_parent_coll = array(
		'uri' => 'sites',
		'mongo' => 'site'
	);

	protected $_fields = array(
		'_id',
		'csm',  // Was this a custom object?
		'lmod', // Last modified (used for revision history date modifications)
		'type', // Object type
		'name', // Object name
		'data', // Object data
		'hist', // Data history
		'site', // Site
	);

	protected $_schemaless = array(
		'data',
		'hist',
	);

	protected $_rules = array(
		'type' => array(
			array('not_empty'),
			array('regex', array(':value', '#^[\w\s]+$#')),
		),
		'lmod' => array(
			array('Mundo::instance_of', array(':value', 'MongoDate'))
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

	protected $_read_only_fields = array(
		'_id',
		'lmod',
		'hist',
		'site'
	);

	protected $_binary_fields = array(
		'hist'
	);

	public function metadata()
	{
		$metadata = parent::metadata();

		return array_merge($metadata, array(
			'help'   => 'Objects allow you to create custom items in the CMS. To create a new object POST to this URI with the object name and field structure. The objects will be visible under /sites/{site_id}/{object_name}. This resource will only list object names and their structure.'
		));
	}

} // END class Model_Object
