<?php

$sourcesDir=__DIR__.'/../../test-classes';

$am=AutoloadManager::___getInstance($sourcesDir, __DIR__.'/../', true);


$sourceFileNames=$am->debugGetSourceFileNames();

$this->ASSERT_TRUE(in_array( $sourcesDir.'/SampleClass.class.php', $sourceFileNames));
$this->ASSERT_TRUE(in_array( $sourcesDir.'/Sources.php', $sourceFileNames));
$this->ASSERT_TRUE(in_array( $sourcesDir.'/lib/Class1.class.php', $sourceFileNames));	
$this->ASSERT_TRUE(in_array( $sourcesDir.'/lib/sub-lib/Sources.php', $sourceFileNames));

		

print_r($sourceFileNames);

