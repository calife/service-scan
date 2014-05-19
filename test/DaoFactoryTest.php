<?php

require(dirname(__FILE__)."/../object/dao/DaoFactory.php");
require_once(dirname(__FILE__)."/../object/model/Entity.php");
require_once(dirname(__FILE__)."/conf/database.conf"); /* credenziali di accesso al database */

$entity= new HostEntity("10.10.203.204");

$factory2 = OracleDaoFactory::getInstance();

$factory2::connect(CONNECTION_STRING , ORA_CON_USERNAME , ORA_CON_PW , ORA_CONNECTION_TYPE_CONNECT);
$hostDao=$factory2::getDao("OracleHostsDao");

// $hostDao->clean();

$entity= new HostEntity("172.16.1.62");
$entity->setHostId("4733");
$entity->setCurrentDate("mer 14 mag 2014, 17.23.19, CEST");

$hostDao->insert($entity);

$entity->setHostname("TEST");
$records=$hostDao->update($entity);

$entity->setHostname(null);
$records=$hostDao->update($entity);


$entity2= new HostEntity("10.10.203.15");
$entity2->setCurrentDate("mer 14 mag 2014, 17.23.19, CEST");
$hostDao->insert($entity2);
$hostDao->insert($entity2);

$res=$hostDao->queryByField("host_id","170");
print_r($res);

$res=$hostDao->deleteByField("host_id","171");

$oracleInstancesHostsDao=$factory2::getDao("OracleInstancesHostsDao");
$oracleInstancesHostsDao->delete(array("host_id"=>"20","instance_id"=>"21"));
$oracleInstancesHostsDao->delete(array("host_id"=>"22"));
$oracleInstancesHostsDao->delete(array("instance_id"=>"23"));
$oracleInstancesHostsDao->delete();

$factory2::close();
