<?php

require_once ( dirname(__FILE__) . "/Entity.php");
require_once ( dirname(__FILE__) . "/Dto.php");

class DTO2EntityConverter {

  protected static $debug=false;

  /**
   * Restituisce un HostDTO, null altrimenti
   **/
  public static function toDTO(HostEntity $entity=null) {

	if(self::$debug) {
	  print_r("###################    INPUT   ##################".PHP_EOL);
	  print_r($entity);
	  print_r("#################################################".PHP_EOL);
	}

	$dto=null;

	if(!is_null($entity)) {

	  $dto=new HostDTO($entity->getNetworkAddress());
	  $dto->setHostEntity($entity);

	} else $dto=null;

	if(self::$debug) {
	  print_r("###################  OUTPUT   ###################".PHP_EOL);
	  print_r($dto);
	  print_r("#################################################".PHP_EOL);
	}

	return $dto;
  }

  /**
   * Restituisce 1 HostEntity e 0..n GenericInstanceEntity
   **/
  public static function fromDTO(HostDTO $dto=null) {

	if(self::$debug) {
	  print_r("###################    INPUT   ##################".PHP_EOL);
	  print_r($dto);
	  print_r("#################################################".PHP_EOL);
	}

	$entity=null;
	if(!is_null($dto) && ! is_null($dto->getHostEntity())) {

	  $entity=$dto->getHostEntity();

	} else $entity=null;

	if(self::$debug) {
	  print_r("###################  OUTPUT   ###################".PHP_EOL);
	  print_r($entity);
	  print_r("#################################################".PHP_EOL);
	}

	return $entity;

  }

}