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
	 * This object contains the list of environments and the supporting 
	 * directory for each environment.
	 * @var array
	 */
	protected $environment_groups;
	
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
	 */
	public function load($group)
	{
		// check if we need to bootstrap the config
		if ( ! $this->bootstrap)
		{
			$this->bootstrap();
		}
		
		$env_group = $this->environment_groups[Kohana::$environment].$group;

		$config             = parent::load($group);
		$environment_config = parent::load($env_group);

		return $this->merge_environment($config, $environment_config);
	}

	/**
	 * Attempts to merge the 2 given Kohana_Config_Group class or 
	 * arrays and returns with the second one taking presendance.
	 *
	 * @param  mixed $config
	 * @param  mixed $environment_config
	 * @return mixed
	 */
	protected function merge_environment($config, $environment_config)
	{
		// is the environment config set
		if (isset($environment_config) AND ! empty($environment_config))
		{
			// is it an array?
			if(is_array($environment_config))
			{
				$config = Arr::merge($config, $environment_config);	
			}
			// is it an instance of Config_Group
			elseif ($environment_config instanceof Config_Group)
			{
				$group = $config->group_name();
				$config = Arr::merge($config->as_array(), $environment_config->as_array());
				$config = new Config_Group($this, $group, $config);
			}
			// we do not know how to handle it, just set it as the environment
			else
			{
				$config = $environment_config;
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
		// boot strap has been called
		$this->bootstrap = TRUE;

		// load the Kohana_Config_Group for the environments
		$this->environment_configs = $this->load('envconfig.environment');
	}
}
