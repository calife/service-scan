<?php

/**
 * Interfaccia di definizione delle operazioni per la gestione delle credenziali di accesso al singolo host.
   ip_address,port_number,user,pass
 **/
interface HostAccessInterface {

  public function getAddress();
  public function getPort();
  public function getUsername();
  public function getPassword();

}


class HostAccess implements HostAccessInterface {

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
