<?php

class AutoloadManager
{
	static protected $___instance;
	
	protected function __constructor()
	{
	}
	
	static public function ___getInstance()
	{
		if(!static::$___instance)
		{
			static::$___instance=new static();
		}
		
		return static::$___instance;
	}
}