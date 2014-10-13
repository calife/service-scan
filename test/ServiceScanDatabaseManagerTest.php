<?php

include_once( dirname(__FILE__) . "/../object/logic/DatabaseManager.php" );
include_once( dirname(__FILE__) . "/../object/logic/ReportFormatter.php" );
include_once( dirname(__FILE__) . "/../object/model/DTO2EntityConverter.php" );

$entities=OracleManager::readFromDatabase();

$dtos=array();
foreach($entities as $entity) {
  $dto= DTO2EntityConverter::toDTO($entity);
  $dtos[]=$dto;
}

$htmlReport=ReportFormatter::generateReport($dtos);
$file = "/home/mpucci/Scrivania/report-fe2.html";
$result=file_put_contents($file, $htmlReport);
