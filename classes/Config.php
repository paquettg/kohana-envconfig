<?php defined('SYSPATH') or die('No direct script access.');

class Config extends Kohana_Config {

	protected $bootstrap = false;
	protected $production;
	protected $staging;
	protected $testing;
	protected $development;

	protected function bootstrap()
	{
		$this->production  = $this->load('config.dir.production');
		$this->staging     = $this->load('config.dir.staging');
		$this->testing     = $this->load('config.dir.testing');
		$this->development = $this->load('config.dir.development');
	}

	public function load($group)
	{
		if ( ! $this->bootstrap)
		{
			$this->bootstrap = true;
			$this->bootstrap();
		}
		try
		{
			switch (Kohana::$environment)
			{
				case Kohana::PRODUCTION:
					$new_group = $this->production.$group;
				case Kohana::STAGING:
					$new_group = $this->staging.$group;
					break;
				case Kohana::TESTING:
					$new_group = $this->testing.$group;
				case Kohana::DEVELOPMENT:
					$new_group = $this->development.$group;
			}
			return parent::load($new_group);
		}
		catch (Kohana_Exception $e)
		{
			try
			{
				return parent::load($group);
			}
			catch (Kohana_Exception $e)
			{
				return null;
			}
		}
	}
}
