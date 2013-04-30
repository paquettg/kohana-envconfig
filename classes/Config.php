<?php defined('SYSPATH') or die('No direct script access.');

class Config extends Kohana_Config {

	protected $bootstrap   = FALSE;
	protected $production  = '';
	protected $staging     = '';
	protected $testing     = '';
	protected $development = '';

	protected function bootstrap()
	{
		$this->production  = $this->load('envconfig.environment_dir.'.Kohana::PRODUCTION);
		$this->staging     = $this->load('envconfig.environment_dir.'.Kohana::STAGING);
		$this->testing     = $this->load('envconfig.environment_dir.'.Kohana::TESTING);
		$this->development = $this->load('envconfig.environment_dir.'.Kohana::DEVELOPMENT);
	}

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
}
