<?php

$sourcesDir=__DIR__.'/../../test-classes';

$am=AutoloadManager::___getInstance($sourcesDir, __DIR__.'/../', true);


$sourceFileNames=$am->debugGetSourceFileNames();

$this->ASSERT_TRUE(in_array( $sourcesDir.'/SampleClass.class.php', $sourceFileNames));
$this->ASSERT_TRUE(in_array( $sourcesDir.'/Sources.php', $sourceFileNames));
$this->ASSERT_TRUE(in_array( $sourcesDir.'/lib/Class1.class.php', $sourceFileNames));	
$this->ASSERT_TRUE(in_array( $sourcesDir.'/lib/sub-lib/Sources.php', $sourceFileNames));

$declaredClasses=$am->debugGetDeclaredClasses();

$this->ASSERT_EQUALS($declaredClasses['SampleNS\SampleClass'], '../test-classes/SampleClass.class.php');
$this->ASSERT_EQUALS($declaredClasses['TestClass'], '../test-classes/Sources.php');
$this->ASSERT_EQUALS($declaredClasses['TestClass2'], '../test-classes/Sources.php');
$this->ASSERT_EQUALS($declaredClasses['C1'], '../test-classes/lib/Class1.class.php');
$this->ASSERT_EQUALS($declaredClasses['NS2\SubClass'], '../test-classes/lib/sub-lib/Sources.php');

$obj=new \SampleNS\SampleClass();
echo "\n".get_class($obj)."\n";


