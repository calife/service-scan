#!/usr/bin/php
<?php

/**
  * Genera un report aggiornato con la situazione delle istanza di Fe e Be
 **/

require_once( dirname(__FILE__) . "/conf/app.conf" );
include_once( dirname(__FILE__) . "/object/logic/ServiceScanReportFormatter.php" );
include_once( dirname(__FILE__) . "/object/logic/ServiceScanDatabaseManager.php" );


$entities=ServiceScanOracleManager::loadFromDatabase();

$dtos=array();
foreach($entities as $entity) {
  $dto= DTO2EntityConverter::toDTO($entity);
  $dtos[]=$dto;
}

if(DEBUG)
  print_r($dtos);

$htmlReport=ServiceScanReportFormatter::generateReport($dtos);
$file = "/home/mpucci/Scrivania/report-fe.html";
$result=file_put_contents($file, $htmlReport);
