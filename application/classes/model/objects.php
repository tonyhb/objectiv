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
		'd',
		'h',
	);

	protected $_fields = array(
		'_id',
		't', // Object type
		'n', // Object name
		'd', // Object data
		'h', // Data history
		's', // Site
	);

	protected $_rules = array(
		't' => array(
			array('not_empty'),
			array('regex', array(':value', '#^[\w\s]+$#')),
		),
		'n' => array(
			array('not_empty'),
		),
		'd' => array(
			array('not_empty'),
		),
		's' => array(
			array('not_empty'),
			array('Mundo::instance_of', array(':value', 'MongoId'))
		),
	);

} // END class Model_Object
