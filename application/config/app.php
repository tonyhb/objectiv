<?php defined('SYSPATH') or die('No direct script access.');

// This allows our environment to dictate which database to connect to
switch(Kohana::$environment)
{
	case Kohana::PRODUCTION:
	case Kohana::STAGING:
		$enviromnent_settings = array(
			'url'  => '',
		);
		break;

	case Kohana::TESTING:
		// Testing database
		$enviromnent_settings = array(
			'url'  => '',
		);
		break;

	case Kohana::DEVELOPMENT:
		// Development database
		$enviromnent_settings = array(
			'url'  => 'epithet.local',
		);
}

return array(
	'salt' => array(1, 4, 7, 8, 9, 10, 11, 18, 24, 28, 30),
) + $enviromnent_settings;
