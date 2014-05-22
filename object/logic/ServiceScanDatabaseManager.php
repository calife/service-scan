<?php

require_once( dirname(__FILE__) . "/../../conf/database.conf" );
require_once(dirname(__FILE__)."/../dao/DaoFactory.php");

/**
 * Interfaccia di definizione dei metodi di accesso alla base dati per la memorizzazione
 * delle istanze scansionate.
 **/
interface AbstractServiceScanDatabaseManager {

  public static function writeToDatabase(array $dtos); /* Popola la base dati con le informazioni censite */
  public static function loadFromDatabase(); /* Legge dalla base dati le informazioni censite */

}

class ServiceScanOracleManager implements AbstractServiceScanDatabaseManager {

  /**
   * Scrittura dell' array dto sul database
   **/
  public static function writeToDatabase(array $dtoArray=array()) {

	$daoFactory = OracleDaoFactory::getInstance(); /* Esegue il caricamento del driver */
	$daoFactory->connect(CONNECTION_STRING , ORA_CON_USERNAME , ORA_CON_PW , ORA_CONNECTION_TYPE_CONNECT);  /* Ottiene una connessione da passare al dao */

	$hostDao=$daoFactory::getDao("OracleHostsDao");

	$hostDao->clean();

	foreach($dtoArray as $dto) {
	  $ent=DTO2EntityConverter::fromDTO($dto);
	  $hostDao->insert($ent);

	}

  }

  /**
   * Lettera dal database 
   **/
  public static function loadFromDatabase() {

	$daoFactory = OracleDaoFactory::getInstance(); /* Esegue il caricamento del driver */
	$daoFactory->connect(CONNECTION_STRING , ORA_CON_USERNAME , ORA_CON_PW , ORA_CONNECTION_TYPE_CONNECT);  /* Ottiene una connessione da passare al dao */

	$hostDao=$daoFactory::getDao("OracleHostsDao");

	$dtoArray=$hostDao->queryAll();

	return array();
  }

}