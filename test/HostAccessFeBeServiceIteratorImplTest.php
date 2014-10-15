<?php

require_once( dirname(__FILE__) . "/../conf/app.conf" );
require_once( dirname(__FILE__) . "/../util/Utils.php" );
require_once( dirname(__FILE__) . "/../util/Keystore.php" );
require_once( dirname(__FILE__) . "/../object/logic/CredentialAccessManager.php" );
require_once( dirname(__FILE__) . "/../object/logic/HostAccessIterator.php");

$instanceList= AccessManager::exportToArray(KEYSTORE);

$iteratore= new HostAccessFeBeServiceIteratorImpl($instanceList);

while($iteratore->hasNextHostAccess())
  $iteratore->getNextHostAccess();

