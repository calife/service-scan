<?php

require_once str_replace('//','/',dirname(__FILE__).'/')."/ServiceScanDao.php";
require_once str_replace('//','/',dirname(__FILE__).'/')."/../../conf/query.inc";  /* SQL stmt */
require_once str_replace('//','/',dirname(__FILE__).'/')."/../model/Entity.php";


class OracleInstancesDao extends OracleDao implements DaoI {

  public function load($pk) /* Get Domain object by primary key */ {

	$parametri = array(':id' => $pk);
    $result=$this->dbref->selectAll(" select * from phpins.instances where instance_id = :id ",$parametri);
	return $result;

  }

  public function queryAll() /* Get all records from table */	{

    $result=$this->dbref->selectAll(" select * from phpins.hosts ");
	return $result;

  }

  public function queryAllOrderBy($orderColumn,$asc=" ASC ") /* Get all records from table ordered by field */	{

    $result=$this->dbref->selectAll(" select * from phpins.instances order by $orderColumn $asc ");
	return $result;

  }

  public function delete($pk) /* Delete record from table */	{

	$parametri = array(':id' => $pk);
    $result=$this->dbref->executeSQL(" delete from phpins.instances where instance_id = :id ",$parametri);
	$this->dbref->commit();
	return $result;

  }

  public function insert($entity=null) /* Insert record to table */	{

	$success=false;
	if(! is_null($entity)) {
	  $parametri = array(':id'=>$entity->getInstanceId());
	  $success=$this->dbref->executeSQL(" insert into phpins.instances(instance_id) values ( :id ) ",$parametri);
	  $this->dbref->commit();
	}
	return $success;

  }

  public function update($entity)	/* Update record in table */ {

	$success=false;
	if(! is_null($entity)) {
	  $parametri = array(':id'=>$entity->getInstanceId());
	  $success=$this->dbref->executeSQL(" UPDATE phpins.instances SET id = :id WHERE instance_id = :id ",$parametri);
	  $this->dbref->commit();
	}
	return $success;

  }

  public function clean() /* Delete all rows */ {

	$success=false;
	$success=$this->dbref->executeSQL(" delete from phpins.instances");
	$this->dbref->commit();

	return $success;

  }

  public function queryByField($fieldName=null,$fieldValue) {

	$sql=" SELECT * FROM phpins.instances ";

	if(!is_null($fieldName)) {
	  $parametri = array(':fieldValue'=> $fieldValue);
	  $sql.=" WHERE $fieldName = :fieldValue ";
	}

	$result=$this->dbref->selectAll($sql,$parametri);
	$this->dbref->commit();

	return $result;

  }

  public function deleteByField($fieldName=null,$fieldValue) {

	$sql=" DELETE FROM phpins.instances ";

	if(!is_null($fieldName)) {
	  $parametri = array(':fieldValue'=> $fieldValue);
	  $sql.=" WHERE $fieldName = :fieldValue ";
	}

	$result=$this->dbref->executeSQL($sql,$parametri);
	$this->dbref->commit();

	return $result;

  }

}


class OracleInstancesHostsDao extends OracleDao implements DaoI {

  public function load($pk) /* Get Domain object by primary key */ {

	$parametri = array(':host_id' => $pk);
    $result=$this->dbref->selectAll(" select * from phpins.instances_hosts where host_id = :host_id ",$parametri);
	return $result;

  }

  public function queryAll() /* Get all records from table */	{

    $result=$this->dbref->selectAll(" select * from phpins.instances_hosts ");
	return $result;

  }

  public function queryAllOrderBy($orderColumn,$asc=" ASC ") /* Get all records from table ordered by field */	{

    $result=$this->dbref->selectAll(" select * from phpins.instances_hosts order by $orderColumn $asc ");
	return $result;

  }

  /**
   * Cancellazione della tabella instances_hosts
   * Input required: $pk array delle chiavi primarie.
   *           Es. array("host_id"=>"2122","instance_id"=>"12")
   *               array("instance_id"=>"9999")
   *
   * Esempio di gestione delle chiavi composte su piu campi
   **/
  public function delete($pk=null) /* Delete record from table */	{
	echo "Starting ".__METHOD__."...".PHP_EOL;

	$sql=" DELETE FROM phpins.instances_hosts ";
	$result=false;

	if( is_array($pk) && (array_key_exists('host_id', $pk) || array_key_exists('instance_id', $pk)) ) {

	  $sql.=" WHERE ";

	  $count=0;
	  foreach($pk as $key=>$value) {
		$sql.=(" ".$key." = "." :$key ");

		if ($count++ < sizeof($pk)-1 )  // FIX
		  $sql.=" AND ";

	  }

	  echo "____".$sql.PHP_EOL;

	  $result=$this->dbref->executeSQL($sql,$pk);
	  $this->dbref->commit();

	} 


	echo "...".__METHOD__." leaving ".PHP_EOL;
	return $result;

  }

  public function insert($entity=null) /* Insert record to table */	{

	$success=false;
	if(! is_null($entity)) {
	  $parametri = array(':host_id'=>$entity->getHostId(),':instance_id' => $entity->getInstanceId());
	  $success=$this->dbref->executeSQL(" insert into phpins.instances_hosts(host_id,instance_id) values ( :host_id , :instance_id ",$parametri);
	  $this->dbref->commit();
	}
	return $success;

  }

  public function update($entity)	/* Update record in table */ {

	$success=false;
	if(! is_null($entity)) {
	  $parametri = array(':host_id'=>$entity->getHostId(),':instance_id' => $entity->getInstanceId());
	  $success=$this->dbref->executeSQL(" UPDATE phpins.instances_hosts SET host_id = :host_id , instance_id = :instance_id ",$parametri);
	  $this->dbref->commit();
	}
	return $success;

  }

  public function clean() /* Delete all rows */ {

	$success=false;
	$success=$this->dbref->executeSQL(" delete from phpins.instances_hosts");
	$this->dbref->commit();

	return $success;

  }

  public function queryByField($fieldName=null,$fieldValue) {

	$sql=" SELECT * FROM phpins.instances_hosts ";

	if(!is_null($fieldName)) {
	  $parametri = array(':fieldValue'=> $fieldValue);
	  $sql.=" WHERE $fieldName = :fieldValue ";
	}

	$result=$this->dbref->selectAll($sql,$parametri);
	$this->dbref->commit();

	return $result;

  }

  public function deleteByField($fieldName=null,$fieldValue) {

	$sql=" DELETE FROM phpins.instances_hosts ";

	if(!is_null($fieldName)) {
	  $parametri = array(':fieldValue'=> $fieldValue);
	  $sql.=" WHERE $fieldName = :fieldValue ";
	}

	$result=$this->dbref->executeSQL($sql,$parametri);
	$this->dbref->commit();

	return $result;

  }

}
