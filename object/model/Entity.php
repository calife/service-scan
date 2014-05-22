<?php

/**
 * Generic instance Entity
 **/

abstract class GenericInstanceEntity {
  protected $instanceName;
  protected $instanceId;

  public function __construct($instanceName='') {
	$this->instanceName=$instanceName;
  }

  public function getInstanceName() {
	return "{$this->instanceName}";
  }

  public function setInstanceName($instanceName) {
	$this->instanceName=$instanceName;
  }

  public function getInstanceId() {
	return "{$this->instanceId}";
  }

  public function setInstanceId($instanceId) {
	$this->instanceId=$instanceId;
  }

  public function __toString() {
	return "Instance Name: ".$this->getInstanceName();
  }

}


/**
 * Singola istanza software di FE, una per ogni tomcat in esecuzione
 **/
class FrontendInstanceEntity extends GenericInstanceEntity  {

  protected $initScript; /* es. Tomcat script, /etc/init.d/tomcat.sh */
  protected $initScriptFileContent; /* Contenuto del file di init */
  protected $catalinaHome; /* es. /usr/local/tomcat6/  */
  protected $isRunning; /* Instance running: true or false */
  protected $existsDeploy; /* Verifica che il deploy esista */
  protected $javaCmdLine; /* Java CmdLine del processo */
  protected $tcpIpPortsArray; /* Array delle porte utilizzate dal FE */
  protected $beInstanceArray; /* Array delle istanze di BE collegate al FE */

  public function __construct($instanceName='') {
	parent::__construct($instanceName);
	$this->initScript=$instanceName;
  }

  public function getInitScript() {
	return "{$this->initScript}";
  }

  public function setInitScript($initScript) {
	$this->initScript=$initScript;
  }

  public function getCatalinaHome() {
	return "{$this->catalinaHome}";
  }

  public function setCatalinaHome($catalinaHome) {
	$this->catalinaHome=$catalinaHome;
  }

  public function isRunning() {
	return "{$this->isRunning}";
  }

  public function setIsRunning($isrunning) {
	$this->isRunning=$isrunning;
  }

  public function setExistsDeploy($existsDeploy) {
	$this->existsDeploy=$existsDeploy;
  }

  public function getExistsDeploy() {
	return "{$this->existsDeploy}";
  }

  public function setInitScriptFileContent($initScriptFileContent) {
	$this->initScriptFileContent=$initScriptFileContent;
  }

  public function getInitScriptFileContent() {
	return "{$this->initScriptFileContent}";
  }

  public function setJavaCmdLine($javaCmdLine) {
	$this->javaCmdLine=$javaCmdLine;
  }

  public function getJavaCmdLine() {
	return "{$this->javaCmdLine}";
  }

  public function getTcpIpPortsArray() {
	return "{$this->tcpIpPortsArray}";
  }

  public function setTcpIpPortsArray($tcpIpPortsArray) {
	return $this->tcpIpPortsArray=$tcpIpPortsArray;
  }

  public function getBeInstanceArray() {
	return $this->beInstanceArray;
  }

  public function setBeInstanceArray($beInstanceArray) {
	return $this->beInstanceArray=$beInstanceArray;
  }

  /**
   * Funzione utility di stampa
   * Ritorna una stringa con l' opzione -D passata alla jvm
   **/
  public function getJavaPropertyFromJavaCmdLine($property) {

	$result;
	$javacmdline=$this->getJavaCmdLine();

	if(! function_exists("getJavaProperty") ) /* because the declaration of the child function is inside the parent, 
                                                 so calling the parent twice is like declaring the child twice…  */  {

	  function getJavaProperty($subject,$pat) {
		$pattern = '/'.$pat.'[^\s]*\s/';
		preg_match($pattern, $subject, $matches);
		return (sizeof($matches)?$matches[0]:"");
	  }

    }

	if(isset($javacmdline)) {
	  if(preg_match('/'.str_replace("/","\/",$this->getCatalinaHome()).'\s/',$javacmdline)) {
		$result=getJavaProperty($javacmdline,$property);
	  }
	}

	return $result;

  }

  public function __toString() {
	$str;
	$str.="Frontend Instance Name: ".trim($this->getInstanceName());
	$str.=" script: ".trim($this->getInitScript());
	$str.=" is running: ".($this->isRunning()?" TRUE ":" FALSE ");
	$str.=" archibus deploy exists: ".($this->getExistsDeploy()?" TRUE ":" FALSE ");
	$str.=" archibus deploy: ".$this->getCatalinaHome();			
  	$str.=" JVM: ".$this->getCatalinaHome();	
	$str.=" ".$this->getJavaPropertyFromJavaCmdLine("Xms");
	$str.=" ".$this->getJavaPropertyFromJavaCmdLine("Xmx");
	$str.=" ".$this->getJavaPropertyFromJavaCmdLine("XX:PermSize");
	$str.=" ".$this->getJavaPropertyFromJavaCmdLine("XX:MaxPermSize");
	$str.=" port: ".$this->getTcpIpPortsArray();			  
	$str.=" Backend: ".$this->getBeInstanceArray();

	return $str;
  }

}

/**
 * Singola istanza software di BE, una per ogni SID
 **/
class BackendInstanceEntity extends GenericInstanceEntity  {

  protected $oracleHome; /* es. /usr/local/tomcat6/  */
  protected $runningAtStartup; /* Instance running: true or false */
  protected $existsDatafile; /* Verifica che il deploy esista */
  protected $isRunning; /* Instance running: true or false */


  public function __construct($instanceName='') {
	parent::__construct($instanceName);
  }

  public function setRunningAtStartup($runningAtStartup) {
	$this->runningAtStartup=$runningAtStartup;
  }

  public function getRunningAtStartup() {
	return $this->runningAtStartup;
  }

  public function setOracleHome($oracleHome) {
	$this->oracleHome=$oracleHome;
  }

  public function getOracleHome() {
	return $this->oracleHome;
  }

  public function isRunning() {
	return "{$this->isRunning}";
  }

  public function setIsRunning($isrunning) {
	$this->isRunning=$isrunning;
  }

  public function setExistsDatafile($existsDatafile) {
	$this->existsDatafile=$existsDatafile;
  }

  public function getExistsDatafile() {
	return $this->existsDatafile;
  }

  public function __toString() {
	return "Backend Instance Name: ".$this->getInstanceName();
  }

}


/**
 * Composite di entity.
 **/
class HostEntity  {

  protected $hostId;
  protected $networkAddress;
  protected $hostname;
  protected $currentDate; /* System Date */
  protected $instanceList; /* GenericInstanceEntity [0..n] */

  public function __construct($networkAddress='') {
	$this->networkAddress=$networkAddress;
  }

  public function getHostId() {
	return $this->hostId;
  }

  public function getNetworkAddress() {
	return $this->networkAddress;
  }

  public function getHostname() {
	return $this->hostname;
  }

  public function getCurrentDate() {
	return $this->currentDate;
  }

  public function getInstanceList() {
	return $this->instanceList;
  }

  public function setHostId($hostId) {
	$this->hostId=$hostId;
  }

  public function setNetworkAddress($networkAddress) {
	$this->networkAddress=$networkAddress;
  }

  public function setHostname($hostname) {
	$this->hostname=$hostname;
  }

  public function setCurrentDate($currentDate) {
	$this->currentDate=$currentDate;
  }

  public function setInstanceList(array $instanceList) /* array di GenericInstanceEntity */ {
	$this->instanceList=$instanceList;
  }

  public function addInstance(GenericInstanceEntity $instance) {
	$this->instanceList[]= $instance;
  }

}


