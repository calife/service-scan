<?php

require_once( dirname(__FILE__) . "/../util/FileCipher.php" );
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
  public static function addInstance(FileCipher $keystore,CredentialAccessInterface $credential);
  public static function removeInstance(FileCipher $keystore,CredentialAccessInterface $credential);
  public static function exportArrayListFromKeystore($keystore);

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

  // TODO: aggiunge un' istanza CredentialAccessInterface al keystore
  public static function addInstance(FileCipher $keystore,CredentialAccessInterface $instance) {
	echo " > ".__METHOD__." < ".PHP_EOL;	
  }

  // TODO: elimina un' istanza CredentialAccessInterface dal keystore
  public static function removeInstance(FileCipher $keystore,CredentialAccessInterface $instance) {
	echo " > ".__METHOD__." < ".PHP_EOL;
  }

  public static function exportArrayListFromKeystore($keystore=null) {

    $paramsArray=array();

	if( ! is_null($keystore) ) {

	  $keystoreManager= new FileCipher($keystore);
	  $clearData= $keystoreManager->exportKeystore();

	  $rows=explode(PHP_EOL, $clearData);

	  foreach($rows as $row) {
		$tmp=explode(',',$row);
		$credential= new CredentialAccess($tmp[0],$tmp[1],$tmp[2],$tmp[3]);

		$paramsArray[]=$credential->toArray();

      }

    }

	return my_unique_array_by_ip(my_sort_array_by_ip($paramsArray));

  }

}
