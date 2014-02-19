<?php

require(dirname(__FILE__)."/../util/Keystore.php");
require(dirname(__FILE__)."/conf/apptest.conf");

Keystore::create(KEYSTORE,CLEARTEXTFILE);

echo Keystore::show(KEYSTORE);
