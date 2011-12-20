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
		'csm',  // Was this a custom object?
		'lmod', // Last modified (used for revision history date modifications)
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

	protected $_metadata = array(
		'read_only' => array(
			'_id',
			'lmod',
			'hist',
			'site'
		),
		'binary' => array('hist')
	);

	protected $_hidden_fields = array(
		'csm' => 0 // This is for internal use only.
	);

	/**
	 * We extend the standard GET method to check whether we're accessing 
	 * a default object (CSS, layouts, snippets etc.) or we're creating a new 
	 * object.
	 *
	 * We could, realistically, make each default object its own model but we 
	 * want the objects in the same mongo collection, which creates this problem 
	 * for us.
	 *
	 */
	public function API_Get($params = array())
	{
		if (get_class($this) != 'Model_Objects')
		{
			// This is a default object, for example CSS.
			$this->set('csm', FALSE);

			// Now we need to ensure we're searching for the correct default 
			// object type.

			$object_type = str_replace('Model_', '', get_class($this));
			$object_type = strtolower($object_type);

			if (array_key_exists('search', $params))
			{
				$position = strpos($params['search'], 'type:');

				if ($position === FALSE)
				{
					$params['search'] .= ',type:'.$object_type;
				}
				else
				{
					// Get the position of the next item
					$next_item_pos = strpos($params['search'], ',', $position);

					$pre_type = substr($params['search'], 0, $position);

					if ($next_item_pos !== FALSE)
					{
						$post_type = substr($params['search'], $next_item_pos);
					}
					else
					{
						$post_type = '';
					}

					$params['search'] = $pre_type.'type:'.$object_type.$post_type;
				}
			}
			else
			{
				$params['search'] = 'type:'.$object_type;
			}

		}
		else
		{
			$this->set('csm', TRUE);
		}

		return parent::API_Get($params);
	}

} // END class Model_Object
