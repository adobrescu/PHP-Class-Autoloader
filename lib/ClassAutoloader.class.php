<?php

/*include class def needed for parsing sources*/
include_once(__DIR__.'/PHPSource.class.php');

class ClassAutoloader
{
	static protected $___instance;/* singleton */
		
	protected $sourcesDirs; /* sources locations to scan*/
	protected $configFileName; /* where to store classes list */
	protected $forceScanFiles; /* if set then do a full scan and do not use the config file*/
	protected $declaredClasses=array();/* keeps a list of declared classes, key => value : class name=> source filename  */
	
	/**
	 * __construct
	 * 
	 * Initialise the object 
	 * 
	 * @param array $sourcesDirs
	 * @param string $configFileName
	 * @param bool $forceScanFiles
	 */
	protected function __construct($sourcesDirs, $configFileName, $forceScanFiles)
	{
	
		if(!is_array($sourcesDirs))
		{
			$sourcesDirs=array($sourcesDirs);
		}
		$this->sourcesDirs=$sourcesDirs;
		
		if(is_dir($configFileName))
		{/*config file default name*/
			$this->configFileName=$configFileName.'/class-autoloader-config.php';
		}
		else
		{
			$this->configFileName=realpath($configFileName);
		}
		
		/*
		- if no $sourcesDirs are specified then declared classes info is loaded from the config file
		 
		- if $sourcesDirs specified $forceScanFiles is false the scan will detect only what files were added or removed to/from the sources and will scan only the new ones
			if class was moved from one file to another:
		 
		- $forceScanFiles forces a full scan of the sources
		 
		*/
			
		$this->forceScanFiles=$this->sourcesDirs?$forceScanFiles:false;/* force scanning make sense only if some dirs were specified */
		
		/* do not include config file if $this->forceScanFiles is set */
		if(!$this->forceScanFiles && is_file($this->configFileName))
		{
			/* include config file */
			include($this->configFileName);
		}
		
		/* register autoload function */
		spl_autoload_register( array( $this, 'autoload' ));
	}
	
	/**
	 * 
	 * ___getInstance
	 * 
	 * Singleton
	 * 
	 * @param array $sourcesDirs
	 * @param string $configFileName
	 * @param bool $forceScanFiles
	 * @return ClassAutoloader
	 */
	static public function ___getInstance($sourcesDirs, $configFileName, $forceScanFiles)
	{
		if(!static::$___instance)
		{
			static::$___instance=new static($sourcesDirs, $configFileName, $forceScanFiles);
			
		}
		
		return static::$___instance;
	}
	
	/**
	 * 
	 * __destruct
	 * 
	 * When the object is released, save classes info in the config file.
	 * ?? Will move this to a function registered with register_shutdown_function
	 */
	public function __destruct()
	{
		/*
		save declared classes in the config file if there was any scanning
		 */
		if($this->sourcesDirs || $this->forceScanFiles)
		{
			file_put_contents($this->configFileName, 
					'<?php'.PHP_EOL.
					'$this->declaredClasses='.var_export($this->declaredClasses, true).
					';'
					);
		}
	}
	
	/**
	 * 
	 * autoload
	 * 
	 * @param string $class
	 */
	public function autoload($class)
	{
		/* scan source files if:
			- sourceDirs is specified
			- or forceScanFiles is set
		 */
		if($this->sourcesDirs || $this->forceScanFiles)
		{
			$this->getDeclaredClasses();
		}
		include_once($this->declaredClasses[$class]);
	}
	

	/**
	 * 
	 * getSourceFileNames
	 * 
	 * Returns a list of php files from a list of dirs. Called recursively. 
	 * 
	 * @param array $sourcesDirs
	 * @return array
	 */
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
					if(pathinfo($file, PATHINFO_EXTENSION)!='php')
					{
						continue;
					}
					$sourceFileNames[$relativePath=$this->mapPath2ThisDir($file)]=$relativePath;
				}
			}
		}
		
		return $sourceFileNames;
	}
	
	/**
	 * 
	 * getDeclaredClasses
	 * 
	 * Scan source files to find all declared classes and builds hte config array (class name => source filename)
	 * 
	 * @return array
	 */
	protected function getDeclaredClasses()
	{
		if($sourceFileNames=$this->getSourceFileNames())
		{
			/*
			 * If there are any declared classes info in the config array (loaded from the config file)
			 * and $sourcesDir is specified
			 * and a force scan is not specified
			 * then do a quick scan:
			 * find what source files were removed and remove their entries from $declaredClasses
			 * find what new files were added, scan them and add their info to $declaredClasses
			*/
			if($this->declaredClasses && $this->sourcesDirs && !$this->forceScanFiles)
			{			
				/* find removed file names */
				foreach($this->declaredClasses as $className=>$sourceFileName)
				{
					if(!isset($sourceFileNames[$sourceFileName]))
					{/* the file was deleted (or just moved) */
						unset($this->declaredClasses[$className]);
						continue;
					}

					
				}
				
				/* remove already scanend files from scan list */
				foreach($this->declaredClasses as $className=>$sourceFileName)
				{
					/* class source file name exists, skip it from scanning */
					
					unset($sourceFileNames[$sourceFileName]);
				}
				
			}
			
			/*
			 * For each file to scan, find its declared classes and add appropriate entry in $declaredClasses
			 */
			foreach($sourceFileNames as $sourceFileName)
			{
				$source=new PHPSource($sourceFileName);
				
				if($declaredClasses=$source->getDeclaredClasses())
				{
					foreach($declaredClasses as $className=>$classInfo)
					{
						$this->declaredClasses[substr($className,1)]=$sourceFileName; /* file names must be relative to the dir containing this class - seee autload method*/
					}
				}
			}
			
		}
		return $this->declaredClasses;
	}
	

	/**
	 * 
	 * mapPath2ThisDir
	 * 
	 * Given a path, it returns its relative path to this file so it can be used/included from here
	 * 
	 * @param string $path
	 * @return string
	 */
	protected function mapPath2ThisDir($path)
	{
		$path=realpath($path);
		$DIR=__DIR__;
		
		/* 
		 * remove from $path and __DIR__ the common starting path 
		 */
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
		
		/*
		 * format relative path:
		 * preppend	to remaining $path as many '../' as '/' are found in remaining __DIR__ + 1
		 */
		
		return str_repeat('../', 
								substr_count($DIR, '/')+1
						).
				$path;
		
	}
	
	/*
	 * Dummy debugging methods
	 */
	public function debugGetSourceFileNames()
	{
		return $this->getSourceFileNames();
	}
	public function debugGetDeclaredClasses()
	{
		return $this->getDeclaredClasses();
	}
}