<?php

require_once str_replace('//','/',dirname(__FILE__).'/')."../driver/OracleDriver.php";
require_once str_replace('//','/',dirname(__FILE__).'/')."/OracleServiceScanDao.php";

/**
 * Interfaccia comune di creazione dei Data Access Object
 **/
interface DaoFactoryI {
  public static function getDao($daoName);
}


/**
 * Sfrutta il late static binding nel metodo getIstance per istanziare una singola istanza di un dato tipo OracleDaoFactory oppure MySQLDaoFactory
 * Lo scopo e' quello di caricare il driver appropriato per la connessione una sola volta.
 **/
class GenericDAOFactory {

  protected static $debug=true;

  private function __construct() /* avoid explicit instantiation */ {
	static::loadDriver();
  }


  public static function getInstance() {

	if(static::$debug) echo __METHOD__.PHP_EOL;

	if(!static::$factoryInstance) /* Non esiste nessuna factory di quel tipo Es. OracleDaoFactory oppure MySQLDaoFactory */  {
	  if(static::$debug) echo "Creating Dao Factory ".PHP_EOL;
	  static::$factoryInstance = new static();
	} else /* Esiste una factory di quel tipo ( static::$factoryInstance ) */ {
	  if(static::$debug) echo "Dao Factory of this type already exists, reusing existing one.".PHP_EOL;
    }
	return  static::$factoryInstance;
  }

}


/**
 * Implementazione Oracle per la creazione dei Data Access Object (oggetti per l' accesso alla base dati per la singola entita')
 **/
class OracleDaoFactory extends GenericDAOFactory implements DaoFactoryI {

  protected static $factoryInstance=null;
  protected static $dbref=null;

  protected static function loadDriver() {
	if(static::$debug) echo __METHOD__.PHP_EOL;
	if(include_once(dirname(__FILE__).'/../driver/OracleDriver.php')) {
	  if(static::$debug) echo "Driver caricato".PHP_EOL;
	  return true;
	} else {
	  throw new Exception('Database driver ' .dirname(__FILE__).'/../driver/OracleDriver.php'. ' for Oracle not found');
	}
  }

  /***
   * Apre una connessione.
   * Utilizza il driver precaricato con il metodo getInstance()
   * Precondizioni:
   * Il metodo deve essere invocato dopo aver caricato il driver appropriato con il metodo getInstance()
   * Postcondizioni:
   * self::$dbref valorizzato per passarlo al metodo getDao
   **/
  public static function connect($host , $user , $pass , $type = ORA_CONNECTION_TYPE_CONNECT) {
	if(static::$debug) echo __METHOD__.PHP_EOL;

	if(!is_null(self::$factoryInstance)) {

	  self::$dbref = new OracleDriver();
	  self::$dbref->connect($host , $user , $pass , $type);
	  self::$dbref->setAutoCommit(FALSE);

	} else {
	  echo "Driver for Oracle not loaded, yet. Required OracleDaoFactory::getInstance()".PHP_EOL;
    }
  }

  /**
   * Chiude la connessione senza invalidare il caricamento del driver
   **/
  public static function close() {
	if(static::$debug) echo __METHOD__.PHP_EOL;
	if(!is_null(self::$dbref)) {
	  self::$dbref->close();
	  self::$dbref=null;
    }
  }

    /*
     * Creates a DAO instance and returns it
	 * The factory function takes as an argument the name of the class to produce
     *
     * @param string $driver the name of the dao you wish to init Es. OracleDriver
     * @returns A concrete dao instance
     */
    public static function getDao($daoName) {
	  if(static::$debug) echo __METHOD__.PHP_EOL;

	  if(is_null(self::$dbref))
		echo "__ Mandatory call to connect() first".PHP_EOL;
	  else  {

		if(class_exists($daoName))
		  return new $daoName(self::$dbref);
		else  echo "Unable to instanciate $daoName";

	  }
    }

}

/**
 * Implementazione MySQL per la creazione dei Data Access Object (oggetti per l' accesso alla base dati per la singola entita')
 **/
class MySQLDaoFactory extends GenericDAOFactory implements DaoFactoryI {

  protected static $factoryInstance=null;

  protected static function loadDriver() {
	if(static::$debug) echo __METHOD__.PHP_EOL;
	if(include_once(dirname(__FILE__).'/../driver/MySQLDriver.php')) {
	  if(static::$debug) echo "Driver caricato".PHP_EOL;
	  return true;
	} else {
	  throw new Exception('Database driver ' .dirname(__FILE__).'/../driver/MySQLDriver.php'. ' for MySQL not found');
	}
  }

  /***
   * Apre una connessione.
   * Il metodo deve essere invocato dopo aver caricato il driver appropriato con il metodo getInstance()
   **/
  public static function connect($host , $user , $pass , $type) {
	if(static::$debug) echo __METHOD__.PHP_EOL;
	echo "TODO: Metodo Mock.".PHP_EOL;
  }

    /*
     * Creates a DAO instance and returns it
	 * The factory function takes as an argument the name of the class to produce
     *
     * @param string $driver the name of the dao you wish to init Es. MySQLDriver
     * @returns A concrete dao instance
     */
    public static function getDao($daoName) {

    }

}




