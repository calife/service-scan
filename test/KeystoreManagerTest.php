<?php

require(dirname(__FILE__)."/../util/FileCipher.php");
require(dirname(__FILE__)."/conf/apptest.conf");

$keystoreManager= new FileCipher(KEYSTORE);

$keystoreManager->importKeystore(CLEARTEXTFILE);

$keystoreManager->exportKeystore();
