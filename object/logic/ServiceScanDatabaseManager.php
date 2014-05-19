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

  public static function writeToDatabase(array $dtos=array()) {
	echo __CLASS__." ".__METHOD__.PHP_EOL;

	$daoFactory = OracleDaoFactory::getInstance(); /* Esegue il caricamento del driver */
	$daoFactory->connect(CONNECTION_STRING , ORA_CON_USERNAME , ORA_CON_PW , ORA_CONNECTION_TYPE_CONNECT);  /* Ottiene una connessione da passare al dao */

	// TODO implementare la logica di scrittura attraverso i dao



  }

  public static function loadFromDatabase() {
	echo __CLASS__." ".__METHOD__.PHP_EOL;


	$factory = OracleDaoFactory::getInstance();
	return array();
  }

}