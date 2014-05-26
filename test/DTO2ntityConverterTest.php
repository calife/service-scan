<?php

/**
  * Client di scansione delle macchine alla ricerca di servizi Fe e Be
 **/

require_once( dirname(__FILE__) . "/../conf/app.conf" );

include_once( dirname(__FILE__) . "/../object/logic/ServiceProvider.php" );
include_once( dirname(__FILE__) . "/../object/logic/ServiceScanner.php" );
include_once( dirname(__FILE__) . "/../object/logic/Command.php" );
include_once( dirname(__FILE__) . "/../object/logic/ServiceScanReportFormatter.php" );
include_once( dirname(__FILE__) . "/../util/Utils.php" );
include_once( dirname(__FILE__) . "/../util/Security.php" );
include_once( dirname(__FILE__) . "/../util/Keystore.php" );
include_once( dirname(__FILE__) . "/../object/logic/CredentialAccessManager.php" );

include_once( dirname(__FILE__) . "/../object/model/DTO2EntityConverter.php" );

require(dirname(__FILE__)."/../object/dao/DaoFactory.php");
require_once(dirname(__FILE__)."/../object/model/Entity.php");
require_once(dirname(__FILE__)."/conf/database.conf"); /* credenziali di accesso al database */

/* $instanceList= AccessManager::exportToArray(KEYSTORE_TEST); */
$instanceList= AccessManager::exportToArray(KEYSTORE);

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

$factory2 = OracleDaoFactory::getInstance();
$factory2::connect(CONNECTION_STRING , ORA_CON_USERNAME , ORA_CON_PW , ORA_CONNECTION_TYPE_CONNECT);

$hostDao=$factory2::getDao("OracleHostsDao");

$hostDao->clean();

foreach($dtoArray as $dto) {
  $ent=DTO2EntityConverter::fromDTO($dto);
  $hostDao->insert($ent);

}

