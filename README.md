# Envconfig

Envconfig module built for the Kohana PHP framework.  Envconfig extends Kohana's Config class to enable loading config files depending on the environment that is currently set. The module can load a unique config file depending on the follow 4 environments: PRODUCTION, STAGING, TESTING, and DEVELOPMENT.

## Requirements

- PHP 5.3+
- Kohana PHP 3.3.x (read the docs!)


## Setup

- Enable the envconfig module in Kohana's bootstrap file.
- Move the Kohana::modules() function befor Kohana::init() in your bootstrap.

## Configuration

### Core (config/config.php)

		'environment.Kohana::PRODUCTION' => 'prod/'

Where should we look for production specific configs?

		'environment.Kohana::STAGING' => 'prod/'

Where should we look for staging specific configs?

		'environment.Kohana::TESTING' => 'dev/'

Where should we look for testing specific configs?

		'environment.Kohana::DEVELOPMENT' => 'dev/'

Where should we look for development specific configs?

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

## NOTE

This module is backwards compatible. If it can not find a given config in the correct environment dir (or there is no environment dir) it will attempt to load the config normaly. It will also return null if the config could not be found at all.

The module will also take into account any config found that is not in the environment specific folders. The hierarchy works as followes:

		(SYSPATH < MODPATH < APPPATH)/config/group.php < (SYSPATH < MODPATH < APPPATH)/config/<env>/group.php

## License
ISC
(c) Copyright 2013 Gilles Paquette

## Links

[Kohana PHP Framework](http://kohanaframework.org/)
