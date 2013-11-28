<?php

include_once "../conf/app.conf";
include_once "../conf/auth.conf";
include_once "../util/utils.php";
include_once "Dto.php";

/**
 * Invoker class for the command pattern implementation.
 * <a href="http://en.wikipedia.org/wiki/Command_pattern">Command Pattern</a>
 *
 * A command object is separately passed to an invoker object, which invokes the command, 
 * and optionally does bookkeeping about the command execution. 
 * Any command object can be passed to the same invoker object. 
 * Both an invoker object and several command objects are held by a client object.
 **/
interface IScanner {
  public function scan();
}


/**
 * Classe di scansione delle istanze tomcat e oracle definite per ciascun host elencato in hosts
 **/
class ServiceScanner implements IScanner {

  private $sshServiceProvider; /* fornisce accesso agli hosts */

  private $hosts; /* lista degli hosts da scansionare */
  private $commands; /* lista dei comandi da eseguire, viene passata dal client con il metodo addCommand */

  public function __construct(array $hosts=null,GenericServiceProvider $serviceProvider,array $commands=null) {
	$this->sshServiceProvider = $serviceProvider;
	$this->hosts = $hosts;
	$this->commands=$commands;
  }

  public function getHosts() {
	return $this->hosts;
  }

  public function setHosts(array $hosts) {
	$this->hosts=$hosts;
  }

  public function getCommands() {
	return $this->commands;
  }

  public function setCommands(AbstractCommand $command) {
	$this->commands=$command;
  }

  public function addCommand(AbstractCommand $command) {
	if(!isset($this->commands)) {
	  $this->commands=array();
	}
	array_push($this->commands,$command);
  }

  /**
   * Esegue lo scan delle istanze per ciascun host nell' hosts array
   * Alla fine dell' operazione verra' restituito un array di oggetti IDTO.
   **/
  public function scan() {

	$resultArray=array(); /* Array di oggetti da servire al client */

	foreach($this->hosts as $host) {
	  try {

		$dto=new HostDTO($host['ip']);
		$resultArray[]= $this->scanSingleHost($host,$dto); /* scansiona il singolo host alla ricerca di servizi */

	  } catch (Exception $e) {
		echo $e->getMessage() . PHP_EOL;
	  }
	}

	return $resultArray;

  }

  /**
   * Apre una connessione per singolo host
   * Popola un HostDTO Data Transfer Object con i servizi di Fe e Be.
   **/
  protected function scanSingleHost($host,IDTO $dto) {
	echo "### Inizio scansione host ".$host['ip']." ### ".PHP_EOL;

	$provider=$this->sshServiceProvider;
	$provider->connect($host['ip'],$host['port'],$host['user'],$host['pass']);

	if($this->executeCommands($dto)) /* esegue i comandi sul singolo host, generic Data Transfer Object */ {
	  $provider->disconnect();
	  echo "### Fine scansione host "." ### ".PHP_EOL;

	  return $dto;
	} else {
	  echo "Commands failed".PHP_EOL;
    }

  }

  public function executeCommands(IDTO $dto) {
	foreach($this->commands as $command) {
	  $result.= $command->perform($dto);
	  $result.=PHP_EOL;
	}

	return $result;
  }

}


