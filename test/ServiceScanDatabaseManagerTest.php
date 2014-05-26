<?php

include_once( dirname(__FILE__) . "/../object/logic/ServiceScanDatabaseManager.php" );
include_once( dirname(__FILE__) . "/../object/logic/DTOFormatter.php" );
include_once( dirname(__FILE__) . "/../object/model/DTO2EntityConverter.php" );

$entities=ServiceScanOracleManager::loadFromDatabase();

$dtos=array();
foreach($entities as $entity) {
  $dto= DTO2EntityConverter::toDTO($entity);
  $dtos[]=$dto;
}

$htmlReport=HostDTOFormatter::generateReport($dtos);
$file = "/home/mpucci/Scrivania/report-fe2.html";
$result=file_put_contents($file, $htmlReport);
