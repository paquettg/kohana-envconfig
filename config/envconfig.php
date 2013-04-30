<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
	'environment_dir' => array
	(
		Kohana::PRODUCTION  => 'prod/',
		Kohana::STAGING     => 'prod/',
		Kohana::TESTING     => 'dev/',
		Kohana::DEVELOPMENT => 'dev/',
	),
);
