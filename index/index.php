<?php

include_once(__DIR__.'/../lib/ClassAutoloader.class.php');

echo '<pre>';
echo '<br><br>Scan '.realpath(__DIR__.'/test-classes').'.<br><br>Paths are relative to the directory where the file that defines ClassAutoloader is stored:<br><br>';
$ca=ClassAutoloader::___getInstance(
		array(__DIR__.'/../test-classes/'),
		__DIR__.'/../class-autoloader-config.php',
		true
		,
		array(__DIR__.'/../test-classes/lib/sub-lib/backup'),
		array('php'),
		array('class.php')
		);

print_r($ca->debugGetDeclaredClasses());

