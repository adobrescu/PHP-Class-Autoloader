<?php

$sourcesDir=__DIR__.'/../../test-classes';

$am=ClassAutoloader::___getInstance($sourcesDir, __DIR__.'/../../', false);


$sourceFileNames=$am->debugGetSourceFileNames();

$this->ASSERT_TRUE(in_array( '../test-classes/SampleClass.class.php', $sourceFileNames));
$this->ASSERT_TRUE(in_array( '../test-classes/Sources.php', $sourceFileNames));
$this->ASSERT_TRUE(in_array( '../test-classes/lib/Class2.class.php', $sourceFileNames));	
$this->ASSERT_TRUE(in_array( '../test-classes/lib/sub-lib/OtherSources.php', $sourceFileNames));

$declaredClasses=$am->debugGetDeclaredClasses();

$this->ASSERT_EQUALS($declaredClasses['SampleNS\SampleClass'], '../test-classes/SampleClass.class.php');
$this->ASSERT_EQUALS($declaredClasses['TestClass'], '../test-classes/Sources.php');
$this->ASSERT_EQUALS($declaredClasses['TestClass2'], '../test-classes/Sources.php');
$this->ASSERT_EQUALS($declaredClasses['C2'], '../test-classes/lib/Class2.class.php');
$this->ASSERT_EQUALS($declaredClasses['NS2\SubClass'], '../test-classes/lib/sub-lib/OtherSources.php');

$obj=new \SampleNS\SampleClass();

echo "1\n";



