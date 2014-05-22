<?php

require_once str_replace('//','/',dirname(__FILE__).'/')."/ServiceScanDao.php";
require_once str_replace('//','/',dirname(__FILE__).'/')."/../../conf/query.inc";  /* SQL stmt */
require_once str_replace('//','/',dirname(__FILE__).'/')."/../model/Entity.php";

/**
 * Classi che operano sulle tabelle Oracle per la memorizzazione dei dati del servizio Service Scan.
 * @author pucci
 * martedì, 20. maggio 2014
 **/

abstract class OracleDao {
  protected $dbref;

  public function __construct($dbref) {
	echo __METHOD__.PHP_EOL;

	$this->dbref=$dbref;

  }

}

class OracleHostsDao extends OracleDao implements DaoI {

  /**
   * Restituisce un oggetto HostEntity , null altrimenti
   **/
  public function load($pk) /* Get Domain object by primary key */ {

	$parametri = array(':id' => $pk);
    $result=$this->dbref->selectAll(" select * from phpins.hosts where host_id = :id ",$parametri);

	if(sizeof($result)===1) {

	  $entity=new HostEntity();
	  $entity->setHostId($result[0]['HOST_ID']);
	  $entity->setHostname($result[0]['HOST_NAME']);
	  $entity->setCurrentDate($result[0]['CURRENT_DATE']);
	  $entity->setNetworkAddress($result[0]['NETWORK_ADDRESS']);

	  /* Estrazione delle istanze collegate all' host */
	  $result=$this->dbref->selectAll("select * from instances_hosts A, fe_instances B where A.INSTANCE_ID=b.INSTANCE_ID AND A.host_id=:id",$parametri);
	  print_r($result);
	  foreach($result as $istanzaFe) {
		$fe=new FrontendInstanceEntity();

		$entity->addInstance($fe);
	  }

	  $result=$this->dbref->selectAll("select * from instances_hosts A, be_instances B where A.INSTANCE_ID=b.INSTANCE_ID AND a.host_id):id",$parametri);
	  foreach($result as $istanzaFe) {
		$fe=new FrontendInstanceEntity();

		$entity->addInstance($fe);
	  }

	  return $entity;

	} else {
	  return null;
	}

  }

  /**
   * Array di HostEntity, null altrimenti
   **/
  public function queryAll() /* Get all records from table */	{

    $result=$this->dbref->selectAll(" select * from phpins.hosts ");

	$resultArray= array();

	for($count=0;$count<sizeof($result);$count++) {

	  $entity=new HostEntity();
	  $entity->setHostId($result[$count]['HOST_ID']);
	  $entity->setHostname($result[$count]['HOST_NAME']);
	  $entity->setCurrentDate($result[$count]['CURRENT_DATE']);
	  $entity->setNetworkAddress($result[$count]['NETWORK_ADDRESS']);

	  $resultArray[]=$entity;

    }

	if(sizeof($resultArray)!==0)
	return $resultArray;
	else return null;

  }

  /**
   * Array di HostEntity, null altrimenti
   **/
  public function queryAllOrderBy($orderColumn,$asc=" ASC ") /* Get all records from table ordered by field */	{

    $result=$this->dbref->selectAll(" select * from phpins.hosts order by $orderColumn $asc ");

	$resultArray= array();

	for($count=0;$count<sizeof($result);$count++) {

	  $entity=new HostEntity();
	  $entity->setHostId($result[$count]['HOST_ID']);
	  $entity->setHostname($result[$count]['HOST_NAME']);
	  $entity->setCurrentDate($result[$count]['CURRENT_DATE']);
	  $entity->setNetworkAddress($result[$count]['NETWORK_ADDRESS']);

	  $resultArray[]=$entity;

    }

	if(sizeof($resultArray)!==0)
	return $resultArray;
	else return null;

  }

  /**
   * Cancellazione sulle tabella HOSTS , INSTANCES_HOSTS , INSTANCES
   **/
  public function delete($pk) /* Delete record from table */	{

	$parametri = array(':id' => $pk);
    $success=$this->dbref->executeSQL(" delete from phpins.instances where instance_id in ( select instance_id from instances_hosts where host_id = :id )  ",$parametri);

	if($success)
	  $success=$this->dbref->executeSQL(" delete from phpins.hosts where host_id = :id  ",$parametri);

	if($success)
	  $this->dbref->commit();
	else 
	  $this->dbref->rollback();

	return $success;

  }

  /**
   * Inserimento sulle tabelle HOSTS e le tabelle HOSTS , INSTANCES_HOSTS , INSTANCES , FE_INSTANCES e BE_INSTANCES
   **/
  public function insert($entity=null) /* Insert record to table */	{

	$success=false;
	  
	if(! is_null($entity) && ($entity instanceof HostEntity ) ) {

	  $host_id=$this->dbref->getNextValueFromSequence("HOSTS_S");
	  $parametri = array(':id'=>$host_id,':host_name' => $entity->getHostname(),':network_address' => $entity->getNetworkAddress(),':current_date' => $entity->getCurrentDate());
	  $success=$this->dbref->executeSQL(" insert into phpins.hosts(host_id,host_name, network_address, current_date) values ( :id , :host_name, :network_address, :current_date) ",$parametri);

	  $instancesList=$entity->getInstanceList();
	  if(! is_null($instancesList) && sizeof($instancesList!=0)) /* inserimento sulle tabelle esterne */ {

		foreach ($instancesList as $elem) {
		  $result=$this->addInstance($host_id,$elem);
		  if(!$result) break; /* Almeno uno STMT in errore , non eseguire la commit  */
		}

	  }

	  if($success)
		$this->dbref->commit();
	  else 
		$this->dbref->rollback();

	}

	return $success;

  }

  /**
   * Aggiunge un istanza collegandola all' HostEntity - Inserimento su INSTANCES , FE_INSTANCES , BE_INSTANCES , INSTANCES_HOSTS
   **/
  protected function addInstance($host_id,GenericInstanceEntity $entity=null) {

	$success=false;

	if(isset($host_id) && ! is_null($entity)) {

	  $instance_id=$this->dbref->getNextValueFromSequence("INSTANCES_S");

	  $parametri = array(':id'=>$instance_id);
	  $success=$this->dbref->executeSQL(" insert into phpins.instances(instance_id) values ( :id ) ",$parametri);

	  if($success) {

		if($entity instanceof FrontendInstanceEntity ) {

		  $parametri = array(':instance_id'=>$instance_id,
							 ':instance_name'=>$entity->getInstanceName(),
							 ':initscriptfilecontent'=>$entity->getInitScript(),
							 ':catalinahome'=>$entity->getCatalinaHome(),
							 ':isrunning'=>$entity->isRunning(),
							 ':existsdeploy'=>$entity->getExistsDeploy(),
							 ':javacmdline'=>$entity->getJavaCmdLine(),
							 ':tcpipportsarray'=>$entity->getTcpIpPortsArray(),
							 ':beinstancearray'=>$entity->getBeInstanceArray());

		  $success=$this->dbref->executeSQL(" insert into phpins.fe_instances(	instance_id,instance_name,initscriptfilecontent,catalinahome,isrunning,existsdeploy,javacmdline,tcpipportsarray,beinstancearray) values ( :instance_id,:instance_name,:initscriptfilecontent,:catalinahome,:isrunning,:existsdeploy,:javacmdline,:tcpipportsarray,:beinstancearray) ",$parametri);

		} else if($entity instanceof BackendInstanceEntity ) {

		  $parametri = array(':instance_id'=>$instance_id,
							 ':instance_name'=>$entity->getInstanceName(),
							 ':runningatstartup'=>$entity->getRunningAtStartup(),
							 ':isrunning'=>$entity->isRunning(),
							 ':oracle_home'=>$entity->getOracleHome(),
							 ':existsdatafile'=>$entity->isRunning());

		  $success=$this->dbref->executeSQL(" INSERT INTO phpins.be_instances( instance_id, instance_name, isrunning , runningatstartup ,existsdatafile , oracle_home ) values ( :instance_id, :instance_name, :runningatstartup , :isrunning , :existsdatafile , :oracle_home) ",$parametri);

		}

	  }

	  if($success) /*  inserimento sulla tabella INSTANCES_HOSTS */ {

		$parametri = array(':instance_id'=>$instance_id,':host_id'=>$host_id);
		$success=$this->dbref->executeSQL(" INSERT INTO phpins.instances_hosts( instance_id, host_id ) values ( :instance_id, :host_id) ",$parametri);

	  }

	}

	return $success;

	}


  /**
   * Cancellazione di tutti gli HostEntity e le InstanceEntity associate
   **/
  public function clean() /* Delete all rows */ {

    $success=$this->dbref->executeSQL(" delete from phpins.instances where instance_id in ( select instance_id from instances_hosts )  ");

	if($success)
	  $success=$this->dbref->executeSQL(" delete from phpins.hosts");

	if($success)
	  $this->dbref->commit();
	else 
	  $this->dbref->rollback();

	return $success;

	}


	/**
	 * TODO
	 **/
	public function update($entity)	/* Update record in table */ {

	  $success=false;
	  if(! is_null($entity)) {
		$parametri = array(':id'=>$entity->getHostId(),':host_name' => $entity->getHostname(),':network_address' => $entity->getNetworkAddress(),':current_date' => $entity->getCurrentDate());
		$success=$this->dbref->executeSQL(" UPDATE phpins.hosts SET host_name = :host_name , network_address = :network_address , current_date = :current_date WHERE host_id = :id ",$parametri);
		$this->dbref->commit();
	  }
	  return $success;

	}

	/**
	 * TODO
	 **/
	public function queryByField($fieldName=null,$fieldValue) {

	  $sql=" SELECT * FROM phpins.hosts ";

	  if(!is_null($fieldName)) {
		$parametri = array(':fieldValue'=> $fieldValue);
		$sql.=" WHERE $fieldName = :fieldValue ";
	  }

	  $result=$this->dbref->selectAll($sql,$parametri);
	  $this->dbref->commit();

	  return $result;

	}

	/**
	 * TODO
	 **/
	public function deleteByField($fieldName=null,$fieldValue) {

	  $sql=" DELETE FROM phpins.hosts ";

	  if(!is_null($fieldName)) {
		$parametri = array(':fieldValue'=> $fieldValue);
		$sql.=" WHERE $fieldName = :fieldValue ";
	  }

	  $result=$this->dbref->executeSQL($sql,$parametri);
	  $this->dbref->commit();

	  return $result;

	}

}



class FEOracleInstancesDao extends OracleDao implements DaoI {

  public function load($pk) /* Get Domain object by primary key */ {

	$parametri = array(':id' => $pk);
    $result=$this->dbref->selectAll(" select * from phpins.fe_instances where instance_id = :id ",$parametri);

	if(sizeof($result)===1) {

	  $entity= new FrontendInstanceEntity();
	  $entity->setInstanceName($result[0]['INSTANCE_NAME']);
	  $entity->setInstanceId($result[0]['INSTANCE_ID']);
	  $entity->setInitScriptFileContent($result[0]['INITSCRIPTFILECONTENT']);
	  $entity->setCatalinaHome($result[0]['CATALINAHOME']);
	  $entity->setIsRunning($result[0]['ISRUNNING']);
	  $entity->setExistsDeploy($result[0]['EXISTSDEPLOY']);
	  $entity->setJavaCmdLine($result[0]['JAVACMDLINE']);
	  $entity->setTcpIpPortsArray($result[0]['TCPIPPORTSARRAY']);
	  $entity->setBeInstanceArray($result[0]['BEINSTANCEARRAY']);

	  return $entity;
	} else { 
	  return null;
	}

  }
  
  /**
   * Array di FrontendInstanceEntity
   **/
  public function queryAll() /* Get all records from table */	{

    $result=$this->dbref->selectAll(" select * from phpins.fe_instances ");

	$resultArray= array();

	for($count=0;$count<sizeof($result);$count++) {

	  $entity= new FrontendInstanceEntity();
	  $entity->setInstanceName($result[$count]['INSTANCE_NAME']);
	  $entity->setInstanceId($result[$count]['INSTANCE_ID']);
	  $entity->setInitScriptFileContent($result[$count]['INITSCRIPTFILECONTENT']);
	  $entity->setCatalinaHome($result[$count]['CATALINAHOME']);
	  $entity->setIsRunning($result[$count]['ISRUNNING']);
	  $entity->setExistsDeploy($result[$count]['EXISTSDEPLOY']);
	  $entity->setJavaCmdLine($result[$count]['JAVACMDLINE']);
	  $entity->setTcpIpPortsArray($result[$count]['TCPIPPORTSARRAY']);
	  $entity->setBeInstanceArray($result[$count]['BEINSTANCEARRAY']);

	  $resultArray[]=$entity;

    }

	if(sizeof($resultArray)!==0)
	return $resultArray;
	else return null;

  }

  /**
   * Array di FrontendInstanceEntity
   **/
  public function queryAllOrderBy($orderColumn,$asc=" ASC ") /* Get all records from table ordered by field */	{

    $result=$this->dbref->selectAll(" select * from phpins.fe_instances order by $orderColumn $asc ");

	$resultArray= array();

	for($count=0;$count<sizeof($result);$count++) {

	  $entity= new FrontendInstanceEntity();
	  $entity->setInstanceName($result[$count]['INSTANCE_NAME']);
	  $entity->setInstanceId($result[$count]['INSTANCE_ID']);
	  $entity->setInitScriptFileContent($result[$count]['INITSCRIPTFILECONTENT']);
	  $entity->setCatalinaHome($result[$count]['CATALINAHOME']);
	  $entity->setIsRunning($result[$count]['ISRUNNING']);
	  $entity->setExistsDeploy($result[$count]['EXISTSDEPLOY']);
	  $entity->setJavaCmdLine($result[$count]['JAVACMDLINE']);
	  $entity->setTcpIpPortsArray($result[$count]['TCPIPPORTSARRAY']);
	  $entity->setBeInstanceArray($result[$count]['BEINSTANCEARRAY']);

	  $resultArray[]=$entity;

    }

	if(sizeof($resultArray)!==0)
	return $resultArray;
	else return null;

  }

  /**
   * Cancellazione dalle tabelle FE_INSTANCES e INSTANCES
   **/
  public function delete($pk) /* Delete record from table */   {

	$parametri = array(':id' => $pk);

	$success=$this->dbref->executeSQL(" delete from phpins.instances where instance_id = :id AND instance_id in ( select instance_id from  phpins.fe_instances ) ",$parametri);

	if($success)
	  $this->dbref->commit();
	else 
	  $this->dbref->rollback();

	return $success;

  }

  /**
   * Inserimento sulle tabelle INSTANCES e FE_INSTANCES
   **/
  public function insert($entity=null) /* Insert record to table */	{

	$success=false;
	  
	if(! is_null($entity) && ($entity instanceof FrontendInstanceEntity ) ) {

	  $instance_id=$this->dbref->getNextValueFromSequence("INSTANCES_S");

	  $parametri = array(':id'=>$instance_id);
	  $success=$this->dbref->executeSQL(" insert into phpins.instances(instance_id) values ( :id ) ",$parametri);

	  if($success) {

		$parametri = array(':instance_id'=>$instance_id,
						   ':instance_name'=>$entity->getInstanceName(),
						   ':initscriptfilecontent'=>$entity->getInitScript(),
						   ':catalinahome'=>$entity->getCatalinaHome(),
						   ':isrunning'=>$entity->isRunning(),
						   ':existsdeploy'=>$entity->getExistsDeploy(),
						   ':javacmdline'=>$entity->getJavaCmdLine(),
						   ':tcpipportsarray'=>$entity->getTcpIpPortsArray(),
						   ':beinstancearray'=>$entity->getBeInstanceArray());

		$success=$this->dbref->executeSQL(" insert into phpins.fe_instances(	instance_id,instance_name,initscriptfilecontent,catalinahome,isrunning,existsdeploy,javacmdline,tcpipportsarray,beinstancearray) values ( :instance_id,:instance_name,:initscriptfilecontent,:catalinahome,:isrunning,:existsdeploy,:javacmdline,:tcpipportsarray,:beinstancearray) ",$parametri);

	  }

	  if($success)
		$this->dbref->commit();
	  else 
		$this->dbref->rollback();

	}

	return $success;

  }

  /**
   * Cancella tutte le istanze di FE dalle tabelle FE_INSTANCES e INSTANCES
   **/
  public function clean() /* Delete all rows */ {

	$success=$this->dbref->executeSQL("delete from PHPINS.INSTANCES where INSTANCE_ID in ( select instance_id from PHPINS.FE_INSTANCES )");

	if($success)
	  $this->dbref->commit();
	else 
	  $this->dbref->rollback();

	return $success;

  }

  /**
   * TODO
   **/
  public function update($entity)	/* Update record in table */ {

	$success=false;
	if(! is_null($entity)) {
	  $parametri = array(':id'=>$entity->getHostId(),':host_name' => $entity->getHostname(),':network_address' => $entity->getNetworkAddress(),':current_date' => $entity->getCurrentDate());
	  $success=$this->dbref->executeSQL(" UPDATE phpins.fe_instances SET host_name = :host_name , network_address = :network_address , current_date = :current_date WHERE host_id = :id ",$parametri);
	  $this->dbref->commit();
	}
	return $success;

  }

  /**
   * TODO
   **/
  public function queryByField($fieldName=null,$fieldValue) {

	$sql=" SELECT * FROM phpins.fe_instances ";

	if(!is_null($fieldName)) {
	  $parametri = array(':fieldValue'=> $fieldValue);
	  $sql.=" WHERE $fieldName = :fieldValue ";
	}

	$result=$this->dbref->selectAll($sql,$parametri);
	$this->dbref->commit();

	return $result;

  }

  /**
   * TODO
   **/
  public function deleteByField($fieldName=null,$fieldValue) {

	$sql=" DELETE FROM phpins.fe_instances ";

	if(!is_null($fieldName)) {
	  $parametri = array(':fieldValue'=> $fieldValue);
	  $sql.=" WHERE $fieldName = :fieldValue ";
	}

	$result=$this->dbref->executeSQL($sql,$parametri);
	$this->dbref->commit();

	return $result;

  }

}


class BEOracleInstancesDao extends OracleDao implements DaoI {

  /**
   * BackendInstanceEntity
   **/
  public function load($pk) /* Get Domain object by primary key */ {

	$parametri = array(':id' => $pk);
    $result=$this->dbref->selectAll(" select * from phpins.be_instances where instance_id = :id ",$parametri);

	if(sizeof($result)===1) {

	  $entity= new BackendInstanceEntity();
	  $entity->setInstanceId($result[0]['INSTANCE_ID']);
	  $entity->setInstanceName($result[0]['INSTANCE_NAME']);
	  $entity->setExistsDatafile($result[0]['EXISTSDATAFILE']);
	  $entity->setRunningAtStartup($result[0]['RUNNINGATSTARTUP']);
	  $entity->setOracleHome($result[0]['ORACLE_HOME']);
	  $entity->setIsRunning($result[0]['ISRUNNING']);

	  return $entity;
	} else { 
	  return null;
	}

  }

  /**
   * Array di BackendInstanceEntity
   **/
  public function queryAll() /* Get all records from table */	{

    $result=$this->dbref->selectAll(" select * from phpins.be_instances ");
	$resultArray= array();

	for($count=0;$count<sizeof($result);$count++) {

	  $entity= new BackendInstanceEntity();
	  $entity->setInstanceId($result[$count]['INSTANCE_ID']);
	  $entity->setInstanceName($result[$count]['INSTANCE_NAME']);
	  $entity->setExistsDatafile($result[$count]['EXISTSDATAFILE']);
	  $entity->setRunningAtStartup($result[$count]['RUNNINGATSTARTUP']);
	  $entity->setOracleHome($result[$count]['ORACLE_HOME']);
	  $entity->setIsRunning($result[$count]['ISRUNNING']);

	  $resultArray[]=$entity;

    }

	if(sizeof($resultArray)!==0)
	return $resultArray;
	else return null;

  }

  /**
   * Array di BackendInstanceEntity
   **/
  public function queryAllOrderBy($orderColumn,$asc=" ASC ") /* Get all records from table ordered by field */	{

    $result=$this->dbref->selectAll(" select * from phpins.be_instances order by $orderColumn $asc ");
	$resultArray= array();

	for($count=0;$count<sizeof($result);$count++) {

	  $entity= new BackendInstanceEntity();
	  $entity->setInstanceId($result[$count]['INSTANCE_ID']);
	  $entity->setInstanceName($result[$count]['INSTANCE_NAME']);
	  $entity->setExistsDatafile($result[$count]['EXISTSDATAFILE']);
	  $entity->setRunningAtStartup($result[$count]['RUNNINGATSTARTUP']);
	  $entity->setOracleHome($result[$count]['ORACLE_HOME']);
	  $entity->setIsRunning($result[$count]['ISRUNNING']);

	  $resultArray[]=$entity;

    }

	if(sizeof($resultArray)!==0)
	return $resultArray;
	else return null;

  }

  /**
   * Cancellazione dalle tabelle BE_INSTANCES e INSTANCES
   **/
  public function delete($pk) /* Delete record from table */   {

	$parametri = array(':id' => $pk);

	$success=$this->dbref->executeSQL(" delete from phpins.instances where instance_id = :id AND instance_id in ( select instance_id from  phpins.be_instances ) ",$parametri);

	if($success)
	  $this->dbref->commit();
	else 
	  $this->dbref->rollback();

	return $success;

  }

  /**
   * Inserimento sulle tabelle INSTANCES e BE_INSTANCES
   **/
  public function insert($entity=null) /* Insert record to table */	{
	
	$success=false;
	  
	if(! is_null($entity) && ($entity instanceof BackendInstanceEntity ) ) {

	  $instance_id=$this->dbref->getNextValueFromSequence("INSTANCES_S");
	  $parametri = array(':id'=>$instance_id);
	  $success=$this->dbref->executeSQL(" insert into phpins.instances(instance_id) values ( :id ) ",$parametri);

	  if($success) {

		$parametri = array(':instance_id'=>$instance_id,
						   ':instance_name'=>$entity->getInstanceName(),
						   ':runningatstartup'=>$entity->getRunningAtStartup(),
						   ':isrunning'=>$entity->isRunning(),
						   ':oracle_home'=>$entity->getOracleHome(),
						   ':existsdatafile'=>$entity->isRunning());

		$success=$this->dbref->executeSQL(" INSERT INTO phpins.be_instances( instance_id, instance_name, isrunning , runningatstartup ,existsdatafile , oracle_home ) values ( :instance_id, :instance_name, :runningatstartup , :isrunning , :existsdatafile , :oracle_home) ",$parametri);

	  }

	  if($success)
		$this->dbref->commit();
	  else 
		$this->dbref->rollback();

	}

	return $success;

  }

  /**
   * Cancella tutte le istanze di BE dalle tabelle BE_INSTANCES e INSTANCES
   **/
  public function clean() /* Delete all rows */ {

	$success=$this->dbref->executeSQL("delete from PHPINS.INSTANCES where INSTANCE_ID in ( select instance_id from PHPINS.BE_INSTANCES )");

	if($success)
	  $this->dbref->commit();
	else 
	  $this->dbref->rollback();

	return $success;

  }

  /**
   * TODO
   **/
  public function update($entity=null)	/* Update record in table */ {

	$success=false;
	if(! is_null($entity)  && ($entity instanceof BackendInstanceEntity ) ) {

	  
	  $parametri = array(':instance_id'=>$entity->getInstanceId(),
						 ':instance_name'=>$entity->getInstanceName(),
                         ':runningatstartup'=>$entity->getRunningAtStartup(),
                         ':isrunning'=>$entity->isRunning(),
                         ':oracle_home'=>$entity->getOracleHome(),
                         ':existsdatafile'=>$entity->isRunning());

	  $success=$this->dbref->executeSQL(" UPDATE phpins.be_instances SET instance_name = :instance_name , runningatstartup = :runningatstartup , isrunning = :isrunning , oracle_home = :oracle_home , existsdatafile = :existsdatafile WHERE instance_id = :instance_id ",$parametri);

     $this->dbref->commit();
	}
	return $success;

  }

  /**
   * TODO
   **/
  public function queryByField($fieldName=null,$fieldValue) {

	$sql=" SELECT * FROM phpins.be_instances ";

	if(!is_null($fieldName)) {
	  $parametri = array(':fieldValue'=> $fieldValue);
	  $sql.=" WHERE $fieldName = :fieldValue ";
	}

	$result=$this->dbref->selectAll($sql,$parametri);
	$this->dbref->commit();

	return $result;

  }

  /**
   * TODO
   **/
  public function deleteByField($fieldName=null,$fieldValue) {

	$sql=" DELETE FROM phpins.be_instances ";

	if(!is_null($fieldName)) {
	  $parametri = array(':fieldValue'=> $fieldValue);
	  $sql.=" WHERE $fieldName = :fieldValue ";
	}

	$result=$this->dbref->executeSQL($sql,$parametri);
	$this->dbref->commit();

	return $result;

  }

}