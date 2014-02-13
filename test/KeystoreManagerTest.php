<?php

require(dirname(__FILE__)."/../util/Keystore.php");
require(dirname(__FILE__)."/conf/apptest.conf");

$keystoreManager= new Keystore(KEYSTORE);

// echo "Importing keystore: ".CLEARTEXTFILE." ".PHP_EOL;
$keystoreManager->importKeystore(CLEARTEXTFILE);

// echo "Exporting keystore: ".KEYSTORE." ".PHP_EOL;
echo $keystoreManager->exportKeystore();
