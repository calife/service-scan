<?php

interface IDTO {

}

/**
 * Data transfer object (DTO)[1][2] is an object that carries data between processes.
 * http://en.wikipedia.org/wiki/Data_transfer_object
 * Il NewHostDTO mappa 1 ad 1 un HostEntity
 **/
class HostDTO implements IDTO {

  protected $hostEntity;

  public function __construct($networkAddress) {
	$this->hostEntity= new HostEntity($networkAddress);
  }

  public function getHostEntity() {
	return $this->hostEntity;
  }

  public function setHostEntity($entity) {
	$this->hostEntity=$entity;
  }

  public function getHostId() {
	return $this->hostEntity->getHostId();
  }

  public function setHostId($hostId) {
	$this->hostEntity->setHostId($hostId);
  }

  public function getNetworkAddress() {
	return $this->hostEntity->getNetworkAddress();
  }

  public function setNetworkAddress($networkAddress) {
	$this->hostEntity->setNetworkAddress($networkAddress);
  }

  public function setHostname($hostname) {
	$this->hostEntity->setHostname($hostname);
  }

  public function getHostname() {
	return $this->hostEntity->getHostname();
  }

  public function setCurrentDate($currentDate) {
	$this->hostEntity->setCurrentDate($currentDate);
  }

  public function getCurrentDate() {
	return $this->hostEntity->getCurrentDate();
  }

  public function getAllInstancesList() {
	return $this->hostEntity->getInstanceList();
  }

 public function getFeInstancesList() {
	if(! function_exists("filtraFe")) {
	  function filtraFe($var) {
		return ($var instanceof FrontendInstanceEntity);
	  }
	}
	if(is_array($this->getAllInstancesList()))
	  return array_filter($this->getAllInstancesList(),"filtraFe");
	else return array();
  }

 public function getBeInstancesList() {
	if(! function_exists("filtraBe")) {
	  function filtraBe($var) {
		return ($var instanceof BackendInstanceEntity);
	  }
	}
	if(is_array($this->getAllInstancesList()))
	  return array_filter($this->getAllInstancesList(),"filtraBe");
	else return array();
  }

  public function addInstance(GenericInstanceEntity $instance) {
	$this->hostEntity->addInstance($instance);
  }

  public function __toString() {

	$str;
	$str.="Network address: ".$this->getNetworkAddress().PHP_EOL;
	$str.="Hostname: ".$this->getHostname().PHP_EOL;
	$str.="Current Date: ".$this->getCurrentDate().PHP_EOL;
	$str.="All Instance List [ ".count($this->getAllInstancesList())." elements] ".PHP_EOL;
	foreach($this->getAllInstancesList() as $instance)
	  $str.=$instance.PHP_EOL;
	$str.=" Fe Instance List [ ".count($this->getFeInstancesList())." elements] ".PHP_EOL;
	foreach($this->getFeInstancesList() as $instance)
	  $str.=$instance.PHP_EOL;
	$str.=" Be Instance List [ ".count($this->getBeInstancesList())." elements] ".PHP_EOL;
	foreach($this->getBeInstancesList() as $instance)
	  $str.=$instance.PHP_EOL;

	$str.="End Instance List. ".PHP_EOL;

	return $str;
  }

}
