<?php defined('SYSPATH') or die('No direct script access.');

class Config_File extends Kohana_Config_File {

	public function load($group)
	{
		$result = parent::load($group);
		if ( ! $result)
		{
			throw new Kohana_Exception("Config file was not found.");
		}

		return $result;
	}
}

