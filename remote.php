#!/usr/bin/php
<?php

/**
  * Client di scansione delle macchine alla ricerca di servizi Fe e Be
 **/

require_once( dirname(__FILE__) . "/conf/app.conf" );

include_once( dirname(__FILE__) . "/object/logic/ServiceProvider.php" );
include_once( dirname(__FILE__) . "/object/logic/ServiceScanner.php" );
include_once( dirname(__FILE__) . "/object/logic/Command.php" );
include_once( dirname(__FILE__) . "/object/logic/DTOFormatter.php" );
include_once( dirname(__FILE__) . "/util/Utils.php" );
include_once( dirname(__FILE__) . "/util/Security.php" );
include_once( dirname(__FILE__) . "/util/Keystore.php" );
include_once( dirname(__FILE__) . "/object/logic/CredentialAccessManager.php" );
include_once( dirname(__FILE__) . "/object/logic/ServiceScanDatabaseManager.php" );

include_once( dirname(__FILE__) . "/object/model/DTO2EntityConverter.php" );


// $instanceList= AccessManager::exportToArray(KEYSTORE);
$instanceList= AccessManager::exportToArray(KEYSTORE_TEST);

$serviceProvider=new SSHServiceProvider(); /* receiver , il cuoco */

$scanner=new ServiceScanner($instanceList,$serviceProvider); /* invoker , il cameriere */

/* Passa all' invoker i comandi da eseguire */
$scanner->addCommand(new GetHostnameCommand($serviceProvider));
$scanner->addCommand(new GetDateCommand($serviceProvider));
$scanner->addCommand(new GetTomcatInitScriptsCommand($serviceProvider));
$scanner->addCommand(new GetCatalinaHomeCommand($serviceProvider));
$scanner->addCommand(new AreTomcatInstancesRunningCommand($serviceProvider));
$scanner->addCommand(new TomcatDeployExistsCommand($serviceProvider));
$scanner->addCommand(new GetJavaCmdLineCommand($serviceProvider));
$scanner->addCommand(new GetTomcatTcpIpPortsCommand($serviceProvider));
$scanner->addCommand(new GetDatabaseRefCommand($serviceProvider));

$scanner->addCommand(new QueryOratabFileCommand($serviceProvider));
$scanner->addCommand(new AreOracleInstancesRunningCommand($serviceProvider));

$dtoArray= $scanner->scan(); /* portata */

print_r($dtoArray);

$htmlReport=HostDTOFormatter::generateReport($dtoArray);
$file = "/home/mpucci/Scrivania/report-fe.html";
$result=file_put_contents($file, $htmlReport);


ServiceScanOracleManager::writeToDatabase($dtoArray);


// TODO generare il report leggendo il $dtoArray dal database


