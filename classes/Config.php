<?php defined('SYSPATH') or die('No direct script access.');

/**
 * An extension to the Kohana config class to allow loading of config file
 * depending on environment. The folders can be modified trough the envconfig
 * config file. The hierarchy is as followes:
 * (SYSPATH < MODPATH < APPPATH)/config/group.php 
 * < 
 * (SYSPATH < MODPATH < APPPATH)/config/<env>/group.php
 *
 * For more information, check the README.md file.
 *
 * @author Gilles Paquette <gilles@gillespaquette.ca>
 */
class Config extends Kohana_Config {
	
	/**
	 * States if the bootstrap function has been called.
	 * @var bool
	 */
	protected $bootstrap   = FALSE;
	/**
	 * The directory that will contain the production specific config files.
	 * @var string
	 */
	protected $production  = '';
	/**
	 * The directory that will contain the staging specific config files.
	 * @var string
	 */
	protected $staging     = '';
	/**
	 * The directory that will contain the testing specific config files.
	 * @var string
	 */
	protected $testing     = '';
	/**
	 * The directory that will contain the development specific config files.
	 * @var string
	 */
	protected $development = '';
	
	/**
	 * Attempts to load a configuration group. Searches all the config sources,
	 * merging all the directives found into a single config group. It will
	 * also add environment specific groups to the current group.
	 *
	 * See [Kohana_Config] for more info
	 *
	 * @param   string  $group  configuration group name
	 * @return  Kohana_Config_Group
	 * @throws  Kohana_Exception
	 * @uses    $this->bootstrap()
	 */
	public function load($group)
	{
		if ( ! $this->bootstrap)
		{
			$this->bootstrap = TRUE;
			$this->bootstrap();
		}

		switch (Kohana::$environment)
		{
			case Kohana::PRODUCTION:
				$env_group = $this->production.$group;
			break;
			case Kohana::STAGING:
				$env_group = $this->staging.$group;
			break;
			case Kohana::TESTING:
				$env_group = $this->testing.$group;
			break;
			case Kohana::DEVELOPMENT:
				$env_group = $this->development.$group;
			break;
		}

		$config             = parent::load($group);
		$environment_config = parent::load($env_group);

		if (isset($environment_config) AND ! empty($environment_config))
		{
			if(is_array($environment_config))
			{
				$config = Arr::merge($config, $environment_config);	
			}
			elseif ($environment_config instanceof Config_Group)
			{
				$config = Arr::merge($config->as_array(), $environment_config->as_array());
				$config = new Config_Group($this, $group, $config);
			}
		}

		return $config;
	}
	
	/**
	 * Loads the configs for the envconfig environment directories.
	 *
	 * @throws Kohana_Exception
	 */
	protected function bootstrap()
	{
		$this->production  = $this->load('envconfig.environment_dir.'.Kohana::PRODUCTION);
		$this->staging     = $this->load('envconfig.environment_dir.'.Kohana::STAGING);
		$this->testing     = $this->load('envconfig.environment_dir.'.Kohana::TESTING);
		$this->development = $this->load('envconfig.environment_dir.'.Kohana::DEVELOPMENT);
	}
}
