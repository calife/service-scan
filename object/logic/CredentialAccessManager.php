<?php

require_once( dirname(__FILE__) . "/../../util/Keystore.php" );
require_once( dirname(__FILE__) . "/../../util/Utils.php" );

/**
 * Interfaccia di definizione delle operazioni per la gestione delle liste delle istanze, dichiara l' interfaccia
 * per l' accesso al keystore.
   ip_address,port_number,user,pass
 **/
interface CredentialAccessInterface {

  public function getAddress();
  public function getPort();
  public function getUsername();
  public function getPassword();

}

interface AccessManagerInterface {

  public static function create($keystore,$clearfile); /* crea un keystore utilizzando il clearfile */
  public static function delete($keystore); /* cancella un keystore */
  public static function show($keystore); /* visualizza il contenuto del keystore */
  public static function export($keystore,$clearfile); /* esporta il contenuto del keystore su un file di testo */
  public static function changekeypasswd($keystore); /* cambia la chiave del keystore */
  public static function exportToArray($keystore); /* esporta il contenuto del keystore in array */
  public static function verify($keystore,$funz=null); /* ritorna true se il keystore e' valido */
  public static function sort($keystore,$funz=null);
  public static function removedup($keystore,$funz=null);

  public static function addentry($keystore,$entry); /* aggiunge un' entry al keystore */
  public static function removeentry($keystore,$entry); /* rimuove un' entry dal keystore */

}

class CredentialAccess implements CredentialAccessInterface {

  private $address;
  private $port;
  private $username;
  private $password;

  public function __construct($address="",$port="",$username="",$password="") {
	$this->address=$address;
	$this->port=$port;
	$this->username=$username;
	$this->password=$password;
  }

  public function getAddress() {return $this->address;}
  public function getPort() {return $this->port;}
  public function getUsername() {return $this->username;}
  public function getPassword() {return $this->password;}

  public function __toString() {
	$str.=" Address: ".$this->getAddress();
	$str.=" Port: ".$this->getPort();
	$str.=" Username: ".$this->getUsername();
	$str.=" Password: ".$this->getPassword();
	return $str;
  }

  public function toArray() {
	$arr=array();
	$arr['ip']=$this->getAddress();
	$arr['port']=$this->getPort();
	$arr['user']=$this->getUsername();
	$arr['pass']=$this->getPassword();
	return $arr;
  }

  /**
   * TODO: implementare un meccanismo di check per cui
   * $this->address è un ip address
   * $this->port è un intero <= 65536
   * $this->username è <> ''
   * $this->password è <> ''
   **/
  public function check() {
	$check=true;
	return true;
  }

}

/**
 * Gestisce le credenziali per l' accesso alle istanze, recuperandole/salvandole dal/sul keystore
 **/
class AccessManager implements AccessManagerInterface {

  public static function create($keystore,$clearfile) /* crea un keystore utilizzando il clearfile */ {
	if(Keystore::import($keystore,$clearfile)) 
	  print_r("Keystore $keystore created.".PHP_EOL);
  }

  public static function delete($keystore) /* cancella un keystore */  {
	if(Keystore::delete($keystore,$clearfile)) 
	  print_r("Keystore $keystore deleted.".PHP_EOL);
  }

  public static function show($keystore) /* visualizza il contenuto del keystore */  {
	$clearData= Keystore::show($keystore);
    print_r("-----BEGIN KEYSTORE DATA MESSAGE-----".PHP_EOL.$clearData.PHP_EOL."-----END KEYSTORE DATA MESSAGE-----".PHP_EOL);
  }

  public static function export($keystore,$clearfile) /* esporta il contenuto del keystore su un file di testo */  {
	if(Keystore::export($keystore,$clearfile))
	  print_r("Keystore $keystore exported.".PHP_EOL);
  }

  public static function changekeypasswd($keystore) /* cambia la chiave del keystore */  {
	if(Keystore::changekeypasswd($keystore))
	  print_r("Keystore $keystore password changed.".PHP_EOL);
  }

  public static function addentry($keystore,$entry) /* aggiunge un' entry al keystore */  {
	if(Keystore::addentry($keystore,$entry))
	  print_r("Keystore $keystore password changed.".PHP_EOL);
  }

  public static function removeentry($keystore,$entry_by_ip) /* rimuove un' entry dal keystore */  {
	if(Keystore::removeentry($keystore,$entry_by_ip))
	  print_r("Keystore $keystore password changed.".PHP_EOL);
  }

  /**
   * Esporta il keystore in un array, esegue una funzione di ordinamento e lo reimporta.
   **/
  public static function sort($keystore,$funz=null) /* ordina le entry del keystore */  {

	if(is_null($funz)) {
	  $funz = function($parametro) {
		return my_sort_array_by_ip($parametro);
      };
	}

	if(Keystore::executeFunction($keystore,$funz))
	  print_r("Keystore $keystore modified via function".PHP_EOL);

  }

  /**
   * Esporta il keystore in un array, esegue una funzione per rimuovere le entry duplicate e lo reimporta.
   **/
  public static function removedup($keystore,$funz=null) /* rimuove le entry duplicate dal keystore */  {

	if(is_null($funz)) {
	  $funz = function($parametro) {
		return my_unique_array_by_ip($parametro);
      };
	}

	if(Keystore::executeFunction($keystore,$funz))
	  print_r("Keystore $keystore modified via function".PHP_EOL);

  }

  /**
   * Restituisce un array di CredentialAccessInterface Objects, ordinato e senza duplicati
   **/
  public static function exportToArray($keystore) {

	$paramsArray=self::exportToBareArray($keystore);

	return my_unique_array_by_ip(my_sort_array_by_ip($paramsArray));

  }

  /**
   * Restituisce un array di CredentialAccessInterface Objects, senza ordinarli e con eventuali duplicati
   **/
  protected  static function exportToBareArray($keystore) {

	$clearData= Keystore::show($keystore);

    $paramsArray=array();
	$rows=explode(PHP_EOL, $clearData);
	foreach($rows as $row) {
	  $tmp=explode(',',$row);
	  $credential= new CredentialAccess($tmp[0],$tmp[1],$tmp[2],$tmp[3]);
	  $paramsArray[]=$credential->toArray();
	}

	return $paramsArray;

  }


  public static function verify($keystore,$funz=null) /* verifica le entry contenute nel keystore */  {

	$verified = true;

	if(is_null($funz)) {
	  $funz = function($parametro) {
		return CredentialAccess::check($parametro);
      };
	}

	$rows=self::exportToBareArray($keystore);

    $paramsArray=array();

	foreach($rows as $row) {

	  $tmp=explode(',',$row);

	  $credential= new CredentialAccess($tmp[0],$tmp[1],$tmp[2],$tmp[3]);

	  if(!$funz($credential)) {
		$verified=false;
		break;
	  }
	}

	$str=$verified?" Keystore $keystore is valid":" Keystore $keystore is Not Valid";

	echo $str.PHP_EOL;

	return $verified;

 }

}
