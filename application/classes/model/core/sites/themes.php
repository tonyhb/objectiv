<?php defined('SYSPATH') or die('No direct script access.');

/**
 *
 * ## What is a theme?
 *
 *    A theme is a central collection of HTML templates, CSS files and 
 *    JS/LESS/CSS files that are bundled together to create a theme package for 
 *    a site on the CMS.
 *
 * ## Why bundle CSS, HTML and JS together?
 *
 *    Because this brings better heirachy to the models (there aren't CSS, JS 
 *    and HTML models as a direct child of sites in the API)
 *
 *    This also allows us to create a default 'theme' package which users can 
 *    learn on.
 *
 *    This will allow us to (eventually) package and possibly themes on a store.
 *
 * @TODO One day I'd like each theme to have an 'activity' field, loggin each 
 *       event (editing or adding an object etc.) in a theme with the user, time 
 *       and object ID. This may be suited more to an activity collection/model, 
 *       though, because when a user changes their name it's easier to search 
 *       one collection than many.
 *
 * @package App
 * @subpackage Theme
 *
 * @author Tony Holdstock-Brown
 */
class Model_Core_Sites_Themes extends Mundo_Object
{
	protected $_collection = 'theme';

	protected $_fields = array(
		'_id',
		'name', // Theme name
		'desc', // Description, optional.
		'img',  // Thumbnail preview of theme
		'lmod', // Last modification date

		'obj.$.id',   // ID of the object in this theme
		'obj.$.type', // Stores the type of object in the theme (HTML, CSS, JS, Less etc.)
		'obj.$.name', // Denormalisation, storing the names of each object in this theme 

		'site',
		'acct'
	);

	protected $_rules = array(
		'name' => array(
			array('not_empty')
		),
		'lmod' => array(
			array('Mundo::instance_of', array(':value', 'MongoDate')),
		),
		'site' => array(
			array('Mundo::instance_of', array(':value', 'MongoId')),
		),
		'acct' => array(
			array('Mundo::instance_of', array(':value', 'MongoId')),
		),
	);

	protected $_parent_coll = array(
		'uri' => 'sites',
		'mongo' => 'site'
	);

	protected $_binary_fields = array(
		'img'
	);

	protected $_read_only_fields = array(
		'lmod'
	);

	public function metadata()
	{
		$metadata = parent::metadata();

		return array_merge($metadata, array(
			'children' => array(
				'css',
				'html',
				'js',
			),
		));
	}

}
