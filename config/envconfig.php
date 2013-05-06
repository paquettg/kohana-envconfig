<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
	'environment_dir' => array
	(
		Kohana::PRODUCTION  => 'prod/',
		Kohana::STAGING     => 'staging/',
		Kohana::TESTING     => 'testing/',
		Kohana::DEVELOPMENT => 'dev/',
	),
	'fallback' => array
	(
		'enabled'       => TRUE,
		Kohana::STAGING => Kohana::PRODUCTION,
		Kohana::TESTING => Kohana::DEVELOPMENT,
	),
);
