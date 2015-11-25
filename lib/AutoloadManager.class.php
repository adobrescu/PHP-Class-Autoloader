<?php

class AutoloadManager
{
	static protected $___instance;
	
	protected function __constructor($sourceDirs, $configDir, $debug)
	{
	}
	
	static public function ___getInstance($sourceDirs, $configDir, $debug)
	{
		if(!static::$___instance)
		{
			static::$___instance=new static($sourceDirs, $configDir, $debug);
		}
		
		return static::$___instance;
	}
}