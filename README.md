# Envconfig

Envconfig module built for the Kohana PHP framework.  Envconfig extends Kohana's Config class to enable loading config files depending on the environment that is currently set. The module can load a unique config file depending on the current value of Kohana::$environment. The module also supports custom environments with proper values in the config file.

## Requirements

- PHP 5.3+
- Kohana PHP 3.3.x (read the docs!)


## Setup

- Enable the envconfig module in Kohana's bootstrap file.
- Move the Kohana::modules() function befor Kohana::init() in your bootstrap.

## Configuration

### Core (config/config.php)

		'environment_dir.Kohana::PRODUCTION' => 'prod/'

Where should we look for production specific configs?

		'environment_dir.Kohana::STAGING' => 'staging/'

Where should we look for staging specific configs?

		'environment_dir.Kohana::TESTING' => 'testing/'

Where should we look for testing specific configs?

		'environment_dir.Kohana::DEVELOPMENT' => 'dev/'

Where should we look for development specific configs?

		'fallback.enabled' => FALSE

Where or not the fallback feature is enabled. More on this feature in the usage area.

		'fallback.Kohana::STAGING' => Kohana::PRODUCTION,

Sets the fallback for the STAGING environment to PRODUCTION by default.

		'fallback.Kohana::TESTING' => Kohana::DEVELOPMENT,

Sets the fallback for the TESTING environment to DEVELOPMENT by default.

## Usage

### Normal

#### 1

Lets say we have a different database for production and development; In my experience this is a rather normal occurrence. Given the default configurations you would create the following 2 files:

		1) APPPATH/config/dev/database.php
		2) APPPATH/config/prod/database.php

Now, when the kohana environment is set to DEVELOPMENT or TESTING it will look at 1 and use the database found in there. If the environment is set to STAGING or PRODUCTION it will look 2 and use the database found in there. 

#### 2

What if you don't have a different database server and all that changes the is the table name? You don't want to have to write all that authentication information so you can have a set up as follows:

		1) APPPATH/config/database.php
		2) APPPATH/config/dev/database.php
		3) APPPATH/config/prod/database.php

You have can the primary authentication information in 1 while 2 and 3 override the table name as needed for the specified environment. This also works with MODPATH and SYSPATH, the hierarchy is specified bellow.

#### 3

By default each environment has its own config directory but this may not be needed. It is possible that your production and testing environment config are identical and in that case you would want to do the following in the config file.

	return array
	(
		'environment_dir' => array
		(
			Kohana::PRODUCTION  => 'prod/',
			Kohana::STAGING     => 'staging/',
			Kohana::TESTING     => 'dev/',
			Kohana::DEVELOPMENT => 'dev/',
		),
	);

This will load the config found in 'dev/' for both the Kohana::Testing and Kohana::Development environments.

### Fallback

#### 1

The module has a fallback feature which allowes you to specify an environment to fall back to. That environment will be used in the case that a config file is not found in the current environment. To enable this feature you must turn the enabled config value to TRUE

	return array
	(
		'fallback' => array
		(
			'enabled' => TRUE,
		),
	);

With the default settings any failure to find a config in STAGING or TESTING will fallback to PRODUCTION or DEVELOPMENT respectively. There for, given the following files

		1) APPPATH/config/dev/database.php
		2) APPPATH/config/prod/database.php

While in the TESTING environment we will load 1 since the testing/ config is not found. While in the STAGING environment we will load 2 since the staging/ config is not found.

#### 2

Modifying the fallbacks is straight foward. Lets say you wish to hav the following fallback trail.

	PRODUCTION > STAGING > TESTING > DEVELOPMENT

In the envconfig file in your APPPATH config you would have the following setup

	return array
	(
		'fallback' => array(
			'enabled'          => TRUE,
			Kohana::PRODUCTION => Kohana::STAGING,
			Kohana::STAGING    => Kohana::TESTING,
			Kohana::TESTING    => Kohana::DEVELOPMENT,
		),
	);

Now if a file is not found in PRODUCTION is will look trough out all the environments for the given config returning NULL if none was found. 

## NOTE

This module is backwards compatible. If it can not find a given config in the correct environment dir (or there is no environment dir) it will attempt to load the config normaly. It will also return null if the config could not be found at all.

The module will also take into account any config found that is not in the environment specific folders. The hierarchy works as followes:

		(SYSPATH < MODPATH < APPPATH)/config/group.php < (SYSPATH < MODPATH < APPPATH)/config/<env>/group.php

The fallback feature will attempt to detect any loops in the fallback array and will terminate, returning null, if a loop condition happens.

## License
ISC
(c) Copyright 2013 Gilles Paquette

## Links

[Kohana PHP Framework](http://kohanaframework.org/)
