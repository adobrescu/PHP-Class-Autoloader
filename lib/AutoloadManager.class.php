<?php

class AutoloadManager
{
	static protected $___instance;
	
	protected $configFileName; //where to store classes list for later use
	protected $forceScanFiles; //if true it doesn't use the config file at all
	
	protected function __construct($sourceDirs, $configFileName, $forceScanFiles)
	{
		if(!is_array($sourceDirs))
		{
			$sourceDirs=array($sourceDirs);
		}
		
		if(is_dir($configFileName))
		{
			$this->configFileName=$configFileName.'/autoload-manager-config.php';
		}
		else
		{
			$this->configFileName=$configFileName;
		}
		
		$this->forceScanFiles=$forceScanFiles;
		
		spl_autoload_register( array( $this, 'autoload' ));
	}
	
	static public function ___getInstance($sourceDirs, $configFileName, $forceScanFiles)
	{
		if(!static::$___instance)
		{
			static::$___instance=new static($sourceDirs, $configFileName, $forceScanFiles);
			
		}
		
		return static::$___instance;
	}
	
	public function autoload($class)
	{
	}
}