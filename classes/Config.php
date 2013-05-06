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
	 *
	 * @var bool
	 */
	protected $bootstrap = FALSE;

	/**
	 * This array contains the list of environments and the supporting 
	 * directory for each environment.
	 *
	 * @var array
	 */
	protected $environment_groups;

	/** 
	 * This array contains the list of fallbacks as well as if fallback
	 * is enabled.
	 *
	 * @var array
	 */
	protected $environment_fallback;
	
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
			$this->_bootstrap();
		}
		
		$config             = parent::load($group);
		$environment_config = $this->_load_environment($group, Kohana::$environment);

		return $this->_merge_environment($config, $environment_config);
	}

	/**
	 * Attempts to merge the 2 given Kohana_Config_Group class or 
	 * arrays and returns with the second one taking presendance.
	 *
	 * @param  mixed $config
	 * @param  mixed $environment_config
	 * @return mixed
	 */
	protected function _merge_environment($config, $environment_config)
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
	protected function _bootstrap()
	{
		// boot strap has been called
		$this->bootstrap = TRUE;

		// load the Kohana_Config_Group for the environments
		$this->environment_groups   = $this->load('envconfig.environment_dir');
		$this->environment_fallback = $this->load('envconfig.fallback');
	}

	/**
	 * Will load the environment specific version of the group that was asked
	 * for. This function has the potential to be recursive if fallback is
	 * enabled.
	 * 
	 * @param  string $group
	 * @param  int    $env
	 * @param  array  $previous
	 * @return mixed
	 * @uses   $this->_fallback()
	 */
	protected function _load_environment($group, $env, $previous = array())
	{
		// we need to have loaded the environment config to load environments
		if ( ! is_array($this->environment_groups))
			return null;

		// load the environment group
		$env_group = $this->environment_groups[$env].$group;
		$config    = parent::load($env_group);

		// shall we check if we need to fallback?
		if ($this->_fallback($config, $env, $previous))
		{
			// prevents circular fallback
			$previous[$env] = TRUE;
			$this->_load_environment($group, $this->environment_fallback[$env], $previous);
		}

		return $config;
	}

	/**
	 * Figures out if the current config set needs to fallback.
	 *
	 * @param  string $group
	 * @param  int    $env
	 * @param  array  $previous
	 * @return bool
	 */
	protected function _fallback($config, $env, $previous)
	{
		if ( ! is_array($this->environment_fallback))
			return FALSE;

		if ($this->environment_fallback['enabled'])
		{
			$tmpconfig = $config;
			if ($tmpconfig instanceof Config_Group)
			{
				$tmpconfig = $tmpconfig->as_array();
			}

			// check if we should fallback
			return (empty($tmpconfig) AND 
			        isset($this->environment_fallback[$env]) AND
			        ! isset($previous[$this->environment_fallback[$env]]));
		}

		return FALSE;
	}
}
