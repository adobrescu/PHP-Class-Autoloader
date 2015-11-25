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
		
		/**
		 * - if no $sourcesDirs are specified then declared classes info is loaded from the config file
		 * 
		 * - if $sourcesDirs specified $forceScanFiles is false the scan will detect only what files were added or removed to/from the sources and will scan only the new ones
		 *	 if class was moved from one file to another:
		 * 
		 * - $forceScanFiles forces a full scan of the sources
		 * 
		 */
		if(!is_array($sourcesDirs))
		{
			$sourcesDirs=array($sourcesDirs);
		}
		$this->sourcesDirs=$sourcesDirs;
		
		if(is_dir($configFileName))
		{
			$this->configFileName=realpath($configFileName.'/autoload-manager-config.php');
		}
		else
		{
			$this->configFileName=realpath($configFileName);
		}
		
		$this->forceScanFiles=$this->sourcesDirs?$forceScanFiles:false;//force scanning make sense only if some dirs were specified
		
		if(is_file($this->configFileName))
		{
			include($this->configFileName);
		}
		
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
		//save declared classes in the config file
		//if there was any scanning
		if($this->sourcesDirs || $this->forceScanFiles)
		{
			file_put_contents($this->configFileName, '<?php'.PHP_EOL.'$this->declaredClasses='.var_export($this->declaredClasses, true).';');
		}
	}
	public function autoload($class)
	{
		//scan source files if:
		//sourceDirs is specified
		//or forceScanFiles is set
		if($this->sourcesDirs || $this->forceScanFiles)
		{
			$this->getDeclaredClasses();
		}
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
					$sourceFileNames[$relativePath=$this->mapPath2ThisDir($file)]=$relativePath;
				}
			}
		}
		
		return $sourceFileNames;
	}
	protected function getDeclaredClasses()
	{
		if($sourceFileNames=$this->getSourceFileNames())
		{
			if($this->declaredClasses && $this->sourcesDirs && !$this->forceScanFiles)
			{
				//If there are declared classes info in the config file
				//and $sourcesDir is specified
				//and a force scan is not necessary
				//then do a quick scan:
				//find what source files were removed and remove their entries from $declaredClasses
				//find what new files were added, scan them and their info to $declaredClasses
				
				//find removed file names
				foreach($this->declaredClasses as $className=>$sourceFileName)
				{
					if(!isset($sourceFileNames[$sourceFileName]))
					{//the file was deleted (or just moved)
						unset($this->declaredClasses[$className]);
						continue;
					}

					
				}
				foreach($this->declaredClasses as $className=>$sourceFileName)
				{
					//class source file name exists, skip it from scanning
					//unset($sourceFileNames[$sourceFileName]);
					unset($sourceFileNames[$sourceFileName]);
				}
				
			}
			
			foreach($sourceFileNames as $sourceFileName)
			{
				$source=new PHPSource($sourceFileName);
				
				if($declaredClasses=$source->getDeclaredClasses())
				{
					foreach($declaredClasses as $className=>$classInfo)
					{
						$this->declaredClasses[substr($className,1)]=$sourceFileName; //file names must be relative to the dir containing this class
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