<?php defined('SYSPATH') or die('No direct script access.');

/**
 * This is the model for CSS objects in the CMS. This has almost the same field
 * structure as a standard CMS object: the only differences being this has a
 * predefined data structure in the data field and has a 'thm' column for the 
 * theme ID.
 *
 * @package App
 * @subpackage Theme
 * @author Tony Holdstock-Brown
 */
class Model_Sites_Themes_CSS extends Model_Sites_Objects {

	protected $_collection = 'css';

	protected $_parent_coll = array(
		'uri' => 'themes',
		'mongo' => 'theme'
	);

	protected $_fields = array(
		'_id',
		'lmod', // Last modified (used for revision history date modifications)
		'type', // Object type
		'name', // Object name
		'data', // Object data
		'hist', // Data history
		'thm',  // Them ID
		'site', // Site
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
		'thm' => array(
			array('Mundo::instance_of', array(':value', 'MongoId'))
		),
		'site' => array(
			array('not_empty'),
			array('Mundo::instance_of', array(':value', 'MongoId'))
		),
	);

	public function metadata()
	{
		return array();
	}
}
