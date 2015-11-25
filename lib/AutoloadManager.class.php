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
	protected function getSourceFileNames($sourcesDirs=null)
	{
		if(!$sourcesDirs)
		{
			$sourcesDirs=$this->sourcesDirs;
		}
		if(!is_array($sourcesDirs))
		{
			$sourcesDirs=array($sourcesDirs);
		}
		
		$sourceFileNames=array();
		
		foreach($sourcesDirs as $sourceDir)
		{
			if($files=glob($sourceDir.'/*'))
			{
				foreach($files as $file)
				{
					if(is_dir($file))
					{
						$sourceFileNames=array_merge($sourceFileNames, $this->getSourceFileNames($file));
						continue;
					}
					$sourceFileNames[]=$file;
				}
			}
		}
		
		return $sourceFileNames;
	}
	protected function getDeclaredClasses()
	{
	}
	
	public function debugGetSourceFileNames()
	{
		return $this->getSourceFileNames();
	}
	public function debugGetDeclaredClasses()
	{
		return $this->getDeclaredClasses();
	}
}