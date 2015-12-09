<?php

include_once(__DIR__.'/../lib/ClassAutoloader.class.php');

$dir=realpath(__DIR__.'/../../laravel/vendor');
$dir=realpath(__DIR__.'/../../ZendFramework-2.4.9');
echo '<pre>';
echo '<br><br>Scan '.$dir.'.<br><br>Paths are relative to the directory where the file that defines ClassAutoloader is stored:<br><br>';
$ca=ClassAutoloader::___getInstance(
		array($dir),
		__DIR__.'/../class-autoloader-config.php',
		true
		,
		array(__DIR__.'/../test-classes/lib/sub-lib/backup'),
		array('php'),
		array('class.php')
		);

print_r($ca->debugGetDeclaredClasses());

