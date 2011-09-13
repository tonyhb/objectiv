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
			'database'  => 'epithet-testing',
		);
		break;

	case Kohana::DEVELOPMENT:
		// Development database
		$enviromnent_settings = array(
			'database'  => 'epithet-dev',
		);
}

return array(
	'mongo_safe'   => TRUE,
) + $enviromnent_settings;
