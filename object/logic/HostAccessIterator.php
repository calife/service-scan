<?php

/**
 * HostAccessIteratorInterface
 *
 * Wednesday, 15. October 2014
 **/

interface HostAccessIteratorInterface {

  public function getCurrentHostAccess();
  public function getNextHostAccess();
  public function hasNextHostAccess();

}


/**
 * Iterator per la scansione degli hosts che ospitano servizi di Frontend e Backend
 **/
class HostAccessFeBeServiceIteratorImpl implements HostAccessIteratorInterface  {

  protected $hostAccessArray;
  protected $currentHostAccessIndex = 0;

  public function __construct(array $hostAccessArray) {
	echo "Start...".__CLASS__." ".__METHOD__.PHP_EOL;
	$this->hostAccessArray = $hostAccessArray;
  }

  public function getCurrentHostAccess() {
	echo "Start...".__CLASS__." ".__METHOD__.PHP_EOL;
	if (($this->currentHostAccessIndex > 0)) {
	  return $this->hostAccessArray[$this->currentHostAccessIndex];
	}
  }

  public function getNextHostAccess() {
	echo "Start...".__CLASS__." ".__METHOD__.PHP_EOL;
	if ($this->hasNextHostAccess()) {
	  return $this->hostAccessArray[++$this->currentHostAccessIndex];
	} else {
	  return NULL;
	}	
  }

  public function hasNextHostAccess() {
	echo "Start...".__CLASS__." ".__METHOD__.PHP_EOL;
	if($this->currentHostAccessIndex < sizeof($this->hostAccessArray) )
	  return TRUE;
	else
	  return FALSE;
  }


}

/**
 * Iterator per la scansione dei soli proxy hosts
 **/
/* class HostAccessProxyIteratorImpl implements HostAccessIteratorInterface  { */

/* } */
