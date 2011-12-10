<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Manages database interaction for the user collection
 *
 * @packaged App
 * @author Tony Holdstock-Brown
 **/
class Model_Users extends App_Model
{

	protected $_collection = 'user';

	protected $_parent_coll = array(
		'uri' => 'accounts',
		'mongo' => 'acct'
	);

	protected $_fields = array(
		'_id',
		'acct',
		'name',
		'email',
		'phone',
		'pw',
		'last.seen',
		'last.ip',
		'sites.$.id',
		'sites.$.name',
		'sites.$.roles.$',
	);

	protected $_rules = array(
		'acct' => array(
			array('Mundo::instance_of', array(':value', 'MongoId')),
		),
		'name' => array(
			array('not_empty'),
			array('min_length', array(':value', 6)),
			array('regex', array(':value', '#^[\w\s-\.]+$#')),
		),
		'email' => array(
			array('not_empty'),
			array('email'),
		),
		'phone' => array(
			array('phone'),
		),
		'pw' => array(
			array('min_length', array(':value', 6)),
			array('not_empty'),
		),
		'last.seen' => array(
			array('Mundo::instance_of', array(':value', 'MongoDate')),
		),
		'last.ip' => array(
			array('ip'),
		),
		'sites.$.id' => array(
			array('Mundo::instance_of', array(':value', 'MongoId')),
		),
		'sites.$.name' => array(
			array('not_empty'),
		),
		'sites.$.roles' => array(
			array('alpha'),
		),
	);

	/**
	 * Hashes the password
	 */
	public static function hash($input, $salt = NULL)
	{
		if ( ! $input)
			return NULL;

		if ( ! $salt)
		{
			// Generate 20 random hex characters using a cryptographically strong algorithm from OpenSSL
			$salt = openssl_random_pseudo_bytes(124);
			$salt = bin2hex($salt);

			// Base convert the hex characters to ASCII
			$salt = base_convert($salt, 10, 35);

			// Strip to 21 characters
			$salt = substr($salt, 0, 21);
		}
		else
		{
			$salt = substr($salt, 0, 21);
		}

		// Add the blowcrypt and cost watermarks
		$salt = "$2a$15$".$salt."$";

		// Hash the input
		$hash = crypt($input, $salt);

		// Remove the blowcrypt and cost watermarks
		$hash = substr($hash, 7);

		// Remove the full-stop inbetween the salt and the hash, which is the 21st character
		return preg_replace('#\.#', '', $hash, 1);
	}

	public function API_Get()
	{
		if ($this->get('_id') !== NULL)
		{
			$this->load(array('pw' => 0, 'csrf' => 0));

			if ( ! $this->loaded())
			{
				throw new App_API_Exception("We could not load the requested account. Please check your request and ensure you are authorised to access this account.", NULL, 400);
			}

			return array(
				'content' => $this->get(),
				'metadata' => array(
					'read_only' => array(
						'_id',
						'acct',
						'sites.$.roles',
					)
				)
			);
		}

		$cursor = $this->find(array('pw' => 0, 'csrf' => 0))->limit(20);

		$return = array(
			'content' => array(),
			'metadata' => array(
				'read_only' => array(
					'_id',
					'acct',
					'sites.$.roles'
				),
				'results' => $cursor->count()
			)
		);

		foreach($cursor as $item)
		{
			$return['content'][] = $item->get();
		}

		return $return;
	}

} // END class Model_User
