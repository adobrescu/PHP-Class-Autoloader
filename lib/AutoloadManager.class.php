<?php

include_once(__DIR__.'/../../plugins-manager/lib/PHPSource.class.php');

class AutoloadManager
{
	static protected $___instance;
	
	protected $sourcesDirs; //source locations to scan
	protected $configFileName; //where to store classes list for later use
	protected $forceScanFiles; //if true it doesn't use the config file at all
	protected $declaredClasses=array();//list of found classes: 'class_name' => 'source_file';
	
	
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
	public function __destruct()
	{
	}
	public function autoload($class)
	{
		$this->getDeclaredClasses();
		
		include_once($this->declaredClasses[$class]);
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
		if($sourceFileNames=$this->getSourceFileNames())
		{
			foreach($sourceFileNames as $sourceFileName)
			{
				$source=new PHPSource($sourceFileName);
				
				if($declaredClasses=$source->getDeclaredClasses())
				{
					foreach($declaredClasses as $className=>$classInfo)
					{
						$this->declaredClasses[substr($className,1)]=$this->mapPath2ThisDir($sourceFileName); //file names must be relative to the dir containing this class
					}
				}
			}
		}
		return $this->declaredClasses;
	}
	protected function mapPath2ThisDir($path)
	{
		$path=realpath($path);
		$DIR=__DIR__;
		
		//remove from $path and __DIR__ the common starting path 
		$pathMinLen=min(strlen($path), strlen($DIR));
		
		for($i=0; $i<$pathMinLen; $i++)
		{
			if($path[$i]!=$DIR[$i])
			{
				break;
			}
		}
		
		
		$path=substr($path, $i);
		$DIR=substr($DIR, $i);
		
		//format relative path:
		//preppend	to remaining $path as many '../' as '/' are found in remaining __DIR__ + 1
		
		return str_repeat('../', 
								substr_count($DIR, '/')+1
						).
				$path;
		
		
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