<?php defined('SYSPATH') or die('No direct script access.');

// This allows our environment to dictate which database to connect to
switch(Kohana::$environment)
{
	case Kohana::PRODUCTION:
	case Kohana::STAGING:
		// Staging & production database
		$enviromnent_settings = array(
			'database'  => 'epithet-production',
		);
		break;

	case Kohana::TESTING:
		// Testing database
		$enviromnent_settings = array(
			'servers' => 'mongodb://localhost:27017,localhost:27018,localhost:27019',
			'database' => 'epithet-testing',
		);
		break;

	case Kohana::DEVELOPMENT:
		// Development database
		$enviromnent_settings = array(
			'servers' => 'mongodb://localhost:27017,localhost:27018,localhost:27019',
			'database'  => 'epithet-dev',
		);
}

return array(
	'connect_options' => array(
		'connect' => TRUE,
		'replicaSet' => 'dev',
	),
	'query_options' => array(
		'safe' => TRUE,
		'fsync' => FALSE,
		'timeout' => 10000,
	),
) + $enviromnent_settings;
