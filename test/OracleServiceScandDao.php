<?php

require(dirname(__FILE__)."/../object/dao/DaoFactory.php");
require_once(dirname(__FILE__)."/../object/model/Entity.php");
require_once(dirname(__FILE__)."/conf/database.conf"); /* credenziali di accesso al database */


$factory2 = OracleDaoFactory::getInstance();
$factory2::connect(CONNECTION_STRING , ORA_CON_USERNAME , ORA_CON_PW , ORA_CONNECTION_TYPE_CONNECT);

/* // TEST MEMORIZZAZIONE BE */
$beEntity= new BackendInstanceEntity("GSP212PRD_DB");
$beDao=$factory2::getDao("BEOracleInstancesDao");

$feEntity= new FrontendInstanceEntity("TOMCAT_GSP212PRD");
$feDao=$factory2::getDao("FEOracleInstancesDao");

$hostDao=$factory2::getDao("OracleHostsDao");

cleanAllFe($feDao);
cleanAllBe($beDao);
cleanAllHosts($hostDao);

$hostEntity= new HostEntity("10.12.4.6");
$hostEntity->setCurrentDate("mer 14 mag 2014, 17.23.19, CEST");
$hostEntity->setHostname("ORAPROD4");

/* for($i=1;$i<=10;$i++) */
/*   $beDao->insert($beEntity); */

/* for($i=1;$i<=10;$i++) */ 
/*   $feDao->insert($feEntity); */


$hostEntity->addInstance($feEntity);
$hostEntity->addInstance($feEntity);
$hostEntity->addInstance($beEntity);
$hostDao->insert($hostEntity);

for($i=3500;$i<=4500;$i++)
  $hostDao->delete($i);


exit;

/* $hostDao->delete(75); */
/* $hostDao->delete(76); */
/* $hostDao->delete(779); */

/* $res=$hostDao->load(103); */
/* if(!is_null($res)) */
/*   print_r($res); */
/* else echo("No record found".PHP_EOL); */

/* $res=$hostDao->queryAll(); */
/* if(!is_null($res)) */
/*   print_r($res); */
/* else echo("No record found".PHP_EOL); */

/* $res=$hostDao->queryAllOrderBy("HOST_ID"); */
/* if(!is_null($res)) */
/*   print_r($res); */
/* else echo("No record found".PHP_EOL); */


// $hostDao->addInstance($hostEntity,$beEntity);

exit;

/* for($i=1;$i<3;$i++) */
/*   $beDao->delete($i); */

/* $res=$beDao->load(159); */
/* if(!is_null($res)) */
/*   print_r($res); */
/* else echo("No record found".PHP_EOL); */

/* $res=$beDao->queryAll(); */
/* if(!is_null($res)) */
/*   print_r($res); */
/* else echo("No record found".PHP_EOL); */

/* $res=$beDao->queryAllOrderBy("INSTANCE_ID"); */
/* if(!is_null($res)) */
/*   print_r($res); */
/* else echo("No record found".PHP_EOL); */


// $feDao->clean();

/* $feDao->insert($feEntity); */

/* $res=$feDao->load(76); */
/* if(!is_null($res)) */
/*   print_r($res); */
/* else echo("No record found".PHP_EOL); */

/* $res=$feDao->queryAll(); */
/* if(!is_null($res)) */
/*   print_r($res); */
/* else echo("No record found".PHP_EOL); */

/* $feDao->delete(231); */
/* $feDao->delete(233); */
/* $feDao->delete(235); */

/* $beDao->delete(179); */

$factory2::close();


function cleanAllFe($dao) {
  $dao->clean();
}

function cleanAllBe($dao) {
  $dao->clean();
}

function cleanAllHosts($dao) {
  $dao->clean();
}

/* // $hostDao->clean(); */

/* $entity= new HostEntity("172.16.1.62"); */
/* $entity->setHostId("4733"); */
/* $entity->setCurrentDate("mer 14 mag 2014, 17.23.19, CEST"); */

/* $hostDao->insert($entity); */

/* $entity->setHostname("TEST"); */
/* $records=$hostDao->update($entity); */

/* $entity->setHostname(null); */
/* $records=$hostDao->update($entity); */


/* $entity2= new HostEntity("10.10.203.15"); */
/* $entity2->setCurrentDate("mer 14 mag 2014, 17.23.19, CEST"); */
/* $hostDao->insert($entity2); */
/* $hostDao->insert($entity2); */

/* $res=$hostDao->queryByField("host_id","170"); */
/* print_r($res); */

/* $res=$hostDao->deleteByField("host_id","171"); */

/* $oracleInstancesHostsDao=$factory2::getDao("OracleInstancesHostsDao"); */
/* $oracleInstancesHostsDao->delete(array("host_id"=>"20","instance_id"=>"21")); */
/* $oracleInstancesHostsDao->delete(array("host_id"=>"22")); */
/* $oracleInstancesHostsDao->delete(array("instance_id"=>"23")); */
/* $oracleInstancesHostsDao->delete(); */

/* $factory2::close(); */
