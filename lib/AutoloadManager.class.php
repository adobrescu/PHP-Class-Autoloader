<?php

class AutoloadManager
{
	static protected $___instance;
	
	protected $sourcesDirs; //source locations to scan
	protected $configFileName; //where to store classes list for later use
	protected $forceScanFiles; //if true it doesn't use the config file at all
	
	protected function __construct($sourcesDirs, $configFileName, $forceScanFiles)
	{
		if(!is_array($sourcesDirs))
		{
			$sourcesDirs=array($sourcesDirs);
		}
		$this->sourcesDirs=$sourcesDirs;
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
	
	static public function ___getInstance($sourcesDirs, $configFileName, $forceScanFiles)
	{
		if(!static::$___instance)
		{
			static::$___instance=new static($sourcesDirs, $configFileName, $forceScanFiles);
			
		}
		
		return static::$___instance;
	}
	
	public function autoload($class)
	{
		
	}
	protected function getSourceFileNames()
	{
	}
	protected function getDeclaredClasses()
	{
	}
	
	public function debugGetSourceFileNames()
	{
		return $this->getSourceFileNames();
	}
}