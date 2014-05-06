<?php

interface IDTO {

}

/**
 * Data transfer object (DTO)[1][2] is an object that carries data between processes.
 * http://en.wikipedia.org/wiki/Data_transfer_object
 **/
class HostDTO implements IDTO {
  protected $networkAddress;
  protected $hostname;
  protected $currentDate; /* System Date */
  protected $instanceList; /* GenericInstanceEntity [0..n] */

  public function __construct($networkAddress) {
	$this->networkAddress=$networkAddress;
  }

  public function getNetworkAddress() {
	return $this->networkAddress;
  }

  public function setNetworkAddress($networkAddress) {
	$this->networkAddress=$networkAddress;
  }

  public function setHostname($hostname) {
	$this->hostname=$hostname;
  }

  public function getHostname() {
	return $this->hostname;
  }

  public function setCurrentDate($currentDate) {
	$this->currentDate=$currentDate;
  }

  public function getCurrentDate() {
	return $this->currentDate;
  }

  public function getAllInstancesList() {
	return $this->instanceList;
  }

 public function getFeInstancesList() {
	if(! function_exists("filtraFe")) {
	  function filtraFe($var) {
		return ($var instanceof FrontendInstanceEntity);
	  }
	}
	return array_filter($this->getAllInstancesList(),"filtraFe");
  }

 public function getBeInstancesList() {
	if(! function_exists("filtraBe")) {
	  function filtraBe($var) {
		return ($var instanceof BackendInstanceEntity);
	  }
	}
	return array_filter($this->getAllInstancesList(),"filtraBe");
  }

  public function addInstance(GenericInstanceEntity $instance) {
	$this->instanceList[]= $instance;
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
