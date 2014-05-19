<?php

include_once( dirname(__FILE__) . "/../object/logic/ServiceScanDatabaseManager.php" );

echo __FILE__.PHP_EOL;

ServiceScanOracleManager::writeToDatabase();

echo "exit;";