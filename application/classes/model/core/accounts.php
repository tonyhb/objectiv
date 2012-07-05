<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Manages database interaction for the account collection
 *
 * @packaged App
 * @author Tony Holdstock-Brown
 **/
class Model_Core_Accounts extends Mundo_Object
{
	protected $_collection = 'account';

	protected $_fields = array(
		'id'              => '_id',
		'account_contact' => 'contact',
		'user[id]'        => 'usr.$.id',
		'user[name]'      => 'usr.$.name',
		'company.name'    => 'company.name',
		'company.reg_no'  => 'company.reg',
		'company.vat_no'  => 'company.vat_no',
		'company.address' => 'company.addr',
		'sites[id]'       => 'sites.$.id',
		'sites[name]'     => 'sites.$.name',
		/*
		'p_sites', // Number of public sites
		't_sites', // Number of sites in total
		'billing.$.date',
		'billing.$.ref',
		'billing.$.amt',
		'billing.$.tax',
		'plan.name',
		'plan.sites',
		'plan.space',
		'plan.cost',
		 */
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

} // END class Model_Account
