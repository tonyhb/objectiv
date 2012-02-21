<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Manages database interaction for the account collection
 *
 * @packaged App
 * @author Tony Holdstock-Brown
 **/
class Model_Accounts extends App_Model
{

	protected $_collection = 'account';

	protected $_parent_coll = array(
		'uri' => NULL,
		'mongo' => NULL
	);

	protected $_fields = array(
		'_id',
		'contact',
		'usr.$.id',
		'usr.$.name',
		'company.name',
		'company.reg',
		'company.vat_no',
		'company.addr',
		'sites.$.id',
		'sites.$.name',
		'p_sites', // Number of public sites
		't_sites', // Number of sites in total
		/*
		'billing.$.date',
		'billing.$.ref',
		'billing.$.amt',
		'billing.$.tax',
		 */
		'plan.name',
		'plan.sites',
		'plan.space',
		'plan.cost',
	);

	protected $_rules = array(
		'contact' => array(
			array('not_empty'),
			array('min_length', array(':value', 6)),
			array('regex', array(':value', '#^[\w\s-\.]+$#')),
		),
		'sites.$.id' => array(
			array('Mundo::instance_of', array(':value', 'MongoId')),
		),
		'usr.$.id' => array(
			array('Mundo::instance_of', array(':value', 'MongoId')),
		),
		'usr.$.name' => array(
			array('not_empty'),
			array('min_length', array(':value', 6)),
			array('regex', array(':value', '#^[\w\s-\.]+$#')),
		),
		'p_sites' => array(
			array('numeric')
		),
		't_sites' => array(
			array('numeric')
		),
	);

	protected $_read_only_fields = array(
		'_id',
		'usr'
	);

	public function metadata()
	{
		$metadata = parent::metadata();

		return array_merge($metadata, array(
			'children' => array(
				'users'
			)
		));
	}

	public function API_Get($params = array())
	{
		$this->set('usr', array(array('id' => App::$user->get('_id'))));

		return parent::API_Get();
	}

} // END class Model_Account
