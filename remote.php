#!/usr/bin/php
<?php

/**
  * Client di scansione delle macchine alla ricerca di servizi Fe e Be
 **/

require_once( dirname(__FILE__) . "/conf/app.conf" );

include_once( dirname(__FILE__) . "/object/ServiceProvider.php" );
include_once( dirname(__FILE__) . "/object/ServiceScanner.php" );
include_once( dirname(__FILE__) . "/object/Command.php" );
include_once( dirname(__FILE__) . "/object/DTOFormatter.php" );
include_once( dirname(__FILE__) . "/util/utils.php" );
include_once( dirname(__FILE__) . "/util/Security.php" );
include_once( dirname(__FILE__) . "/util/FileCipher.php" );
include_once( dirname(__FILE__) . "/object/CredentialAccessManager.php" );

$paramsArr= AccessManager::exportToArray(KEYSTORE);

/* print_r($paramsArr); */
/* exit; */

$serviceProvider=new SSHServiceProvider(); /* receiver , il cuoco */

$scanner=new ServiceScanner($paramsArr,$serviceProvider); /* invoker , il cameriere */

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



