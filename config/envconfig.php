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
);
