<?php

require_once( dirname(__FILE__) . "/../util/Keystore.php" );
require_once( dirname(__FILE__) . "/../util/utils.php" );

/**
 * Interfaccia di definizione delle operazioni per la gestione delle liste delle istanze
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
  public static function addentry($keystore,$entry); /* aggiunge un' entry al keystore */
  public static function removeentry($keystore,$entry); /* rimuove un' entry dal keystore */
  public static function exportToArray($keystore); /* esporta il contenuto del keystore in array */

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

}

/**
 * Gestisce le credenziali per l' accesso alle istanze, recuperandole/salvandole dal/sul keystore
 **/
class AccessManager implements AccessManagerInterface {

  public static function create($keystore,$clearfile) /* crea un keystore utilizzando il clearfile */ {
	if(Keystore::create($keystore,$clearfile)) 
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
	Keystore::export($keystore,$clearfile);
  }

  public static function changekeypasswd($keystore) /* cambia la chiave del keystore */  {
	echo " > ".__METHOD__." < ".PHP_EOL;
  }

  public static function addentry($keystore,$entry) /* aggiunge un' entry al keystore */  {
	echo " > ".__METHOD__." < ".PHP_EOL;
  }

  public static function removeentry($keystore,$entry) /* rimuove un' entry dal keystore */  {
	echo " > ".__METHOD__." < ".PHP_EOL;
  }

  /**
   * Restituisce un array di CredentialAccessInterface Objects
   **/
  public static function exportToArray($keystore) {

	$clearData= Keystore::show($keystore);

    $paramsArray=array();
	$rows=explode(PHP_EOL, $clearData);
	foreach($rows as $row) {
	  $tmp=explode(',',$row);
	  $credential= new CredentialAccess($tmp[0],$tmp[1],$tmp[2],$tmp[3]);
	  $paramsArray[]=$credential->toArray();
	}

	return my_unique_array_by_ip(my_sort_array_by_ip($paramsArray));

  }

}
