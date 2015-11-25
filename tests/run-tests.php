<?php

header('Content-Type: text/html; charset=utf-8');

include(__DIR__.'/unit-testing-lib/DebugBatchTest.class.php');
include_once(__DIR__.'/../lib/AutoloadManager.class.php');

define('DEBUG', true);



new DebugBatchTest(__DIR__.'/unit-testing', 'DebugTest');


