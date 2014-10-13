<?php

require_once ( dirname(__FILE__) . "/../model/Entity.php");

/**
 * Classe astratta di definizione dei comandi eseguibile dal receiver (GenericServiceProvider)
 * I comandi vengono eseguiti sul IDTO object
 **/
abstract class AbstractCommand {

  protected $serviceProvider;

  function __construct(GenericServiceProvider $serviceProvider=null) {
	if(isset($serviceProvider)) {
	  $this->serviceProvider = $serviceProvider;
	} else {
	  throw new Exception(' Exception '.get_class($this).'__constructor($serviceProvider), undefined serviceProvider. ');
	}
  }

  abstract function perform(IDTO $dto=null);
}

/**
 * Composite di AbstractCommand
 * TODO testare
 **/
class MacroCommand extends AbstractCommand {

  private $commands=array();

  public function addCommandToMacro(AbstractCommand $command) {
	$this->commands[] = $command;
  }

  public function perform(IDTO $dto=null) {
	foreach($this->commands as $command) {
	  echo $command->perform().PHP_EOL;
	}
  }

}

/**
 * Classi concrete di definizione dei comandi eseguibili dal receiver (SSHServiceProvider)
 **/
class GetHostnameCommand  extends AbstractCommand {
  const COMMAND_GET_HOSTNAME="hostname";
  public function perform(IDTO $dto=null) {
	$dto->setHostname($this->serviceProvider->exec(self::COMMAND_GET_HOSTNAME));
	return TRUE;
  }
}

class GetDateCommand  extends AbstractCommand {
  const COMMAND_GET_DATE="date";

  public function perform(IDTO $dto=null) {
	$dto->setCurrentDate($this->serviceProvider->exec(self::COMMAND_GET_DATE));

	return TRUE;
  }
}


/** Tomcat specific command **/

class GetTomcatInitScriptsCommand  extends AbstractCommand {

  const COMMAND_GET_TOMCAT_SCRIPTS="if [ -d /etc/init.d ]; then \
for i in `grep -iRl 'java' /etc/init.d | grep -v 'hsqldb' | grep -v 'jexec' | grep -v 'red5' ` ; do \
echo \$i; \
done; \
fi;";

  public function perform(IDTO $dto=null) {

	$initScriptsArray = array_filter(explode(PHP_EOL,$this->serviceProvider->exec(self::COMMAND_GET_TOMCAT_SCRIPTS)),"notEmpty");

	foreach($initScriptsArray as $initScriptName) {
	  $instanceName=preg_replace('/\.sh/','',preg_replace('/\/etc\/init.d\//','',$initScriptName));
      $instance=new FrontendInstanceEntity($instanceName);
	  $instance->setInitScript($initScriptName);
	  $dto->addInstance($instance);
	}

	return TRUE;
  }
}

class GetCatalinaHomeCommand  extends AbstractCommand {

  private function getCatalinaHomeFromTomcatScript($fileContent) {
	preg_match('/(INSTANCE_)?NAME=.*/', $fileContent, $matches1);
	if(!is_null($matches1) && is_array($matches1) && sizeof($matches1)>0) {
	  $name = preg_replace('/(INSTANCE_)?NAME=/', '', $matches1[0]);
	  preg_match('/CATALINA_HOME=.*/', $fileContent, $matches2);
	  if(!is_null($matches2) && is_array($matches2) ) {
		$catalinaHome = preg_replace('/\$(INSTANCE_)?NAME/', $name, preg_replace('/CATALINA_HOME=/', '', $matches2[0]));
		return trim(str_replace("\"","",$catalinaHome));
	  }
	}
  }

  public function perform(IDTO $dto=null) {
	foreach($dto->getFeInstancesList() as $instances) {
	  $fileName=trim($instances->getInitScript());
	  $fileContent = $this->serviceProvider->exec(" cat $fileName");
	  $catalinaHome=$this->getCatalinaHomeFromTomcatScript($fileContent);
	  $instances->setCatalinaHome($catalinaHome);
	}
	return TRUE;
  }
}

class AreTomcatInstancesRunningCommand extends AbstractCommand {

  private function isTomcatInstanceRunning($catalinaHome,$javaRunningProcessesArr) {
	$result= $this->my_array_search("/\s-Dcatalina.home=".str_replace("/","\/",$catalinaHome)."\s/",$javaRunningProcessesArr);
	return $result;
  }

  /* Estrae l' elenco dei processi java in esecuzione */
  private function getJavaProcesses() {
	$tomcatArray = array_filter(explode(PHP_EOL, $this->serviceProvider->exec("ps auxww|grep java|grep -v grep")),"notEmpty");
	return array_map("trim",$tomcatArray);
  }

  private function my_array_search($needle,$haystack) {
    foreach($haystack as $key=>$value) {
	  if(preg_match($needle,$haystack[$key])) {
		return 1;
	  }
    }
    return 0;
  }

  public function perform(IDTO $dto=null) {

	$javaRunningProcessesArr=$this->getJavaProcesses(); /* estrae l' array con i processi java in esecuzione */

	foreach($dto->getFeInstancesList() as $instances) {
	  $catalinaHome=$instances->getCatalinaHome();
	  $isRunning=$this->isTomcatInstanceRunning($catalinaHome,$javaRunningProcessesArr);
	  $instances->setIsRunning($isRunning);
	}
	return TRUE;
  }
}


class TomcatDeployExistsCommand extends AbstractCommand {

  private function remoteFileExists($fileName) {

	$cmd=" if [ ! -d $fileName ]; then echo 0; else echo 1; fi; ";

	$fileExists=$this->serviceProvider->exec($cmd);

	return $fileExists==1?1:0;
  }


  public function perform(IDTO $dto=null) {

	foreach($dto->getFeInstancesList() as $instances) {
	  $catalinaHome=$instances->getCatalinaHome();
	  $instances->setExistsDeploy($this->remoteFileExists($catalinaHome."/webapps/archibus"));
	}

	return TRUE;
  }

}

class GetInitScriptFileContentCommand extends AbstractCommand {

  public function perform(IDTO $dto=null) {

	foreach($dto->getFeInstancesList() as $instances) {
	  $fileName=trim($instances->getInitScript());
	  $fileContent = $this->serviceProvider->exec(" if [ -r $fileName ]; then cat $fileName ; fi;");
	  $instances->setInitScriptFileContent($fileContent);
	}

	return TRUE;

  }
}


class GetJavaCmdLineCommand extends AbstractCommand {

  /* Estrae l' elenco dei processi java in esecuzione */
  private function getJavaProcesses() {
	$tomcatArray = array_filter(explode(PHP_EOL, $this->serviceProvider->exec("ps auxww|grep java|grep -v grep")),"notEmpty");
	return array_map("trim",$tomcatArray);
  }

  private function my_array_search($needle,$haystack) {
    foreach($haystack as $key=>$value) {
	  if(preg_match($needle,$haystack[$key])) {
		return 1;
	  }
    }
    return 0;
  }

  public function perform(IDTO $dto=null) {

	$javaRunningProcessesArr=$this->getJavaProcesses();

	foreach($dto->getFeInstancesList() as $instances) {
	  $catalinaHome=$instances->getCatalinaHome();

	  foreach($javaRunningProcessesArr as $process) {
		if(preg_match("/\s-Dcatalina.home=".str_replace("/","\/",$catalinaHome)."\s/",$process))
		  $instances->setJavaCmdLine($process);

	  }
	}

	return TRUE;

  }
}

class GetTomcatTcpIpPortsCommand  extends AbstractCommand {


  function getTomcatTcpIpPorts($basedir) {
	$tmp="";
	try {
	  $xml = simplexml_load_string($this->serviceProvider->exec(" if [ -r  $basedir/conf/server.xml ]; then cat $basedir/conf/server.xml; fi; "));

	  if($xml) {
		foreach ($xml->xpath('/Server/Service[@name="Catalina"]/Connector[@port and not(@protocol="AJP/1.3")] ') as $connector) {
		  $tmp.=$connector['port']." ";
		}
	  } else {
		$tmp='Error while parsing the document';
	  }

	} catch(Exception $e) {
	  $tmp=$e;
	}
	return $tmp;
  }


  public function perform(IDTO $dto=null) {

	foreach($dto->getFeInstancesList() as $instances) {
	  if($instances->getExistsDeploy()) {
		$catalinaHome=$instances->getCatalinaHome();
		$instances->setTcpIpPortsArray($this->getTomcatTcpIpPorts($catalinaHome));
	  }
	}

	return TRUE;
  }

}


class GetDatabaseRefCommand  extends AbstractCommand {

  /*  * Restituisce un array con le stringhe di connessioni attive */
  private function getDatabaseRef($basedir) {
	$result=array();
	try {

	  $xml = simplexml_load_string($this->serviceProvider->exec(" if [ -f  $basedir/webapps/archibus/WEB-INF/config/afm-projects.xml ]; then cat $basedir/webapps/archibus/WEB-INF/config/afm-projects.xml; fi; "));
	  if($xml) {
		foreach ($xml->xpath('/afm-projects/project/databases[not(preceding-sibling::*/@active="false")]/database[@role="data"]/engine') as $engine) {
		  array_push($result,$engine->jdbc['url']);
		}
	  } else {
		array_push($result,'No database connection detected');
	  }

	} catch(Exception $e) {
	  array_push($result,$e);
	}
	return $result;
  }

  /**
   * $connectionStringArr, array con le stringhe di connessione, ciascuna entry è nella forma seguente:
   *  Sybase jdbc:sybase:Tds:192.168.200.15:49152
   *  Oracle jdbc:oracle:thin:@192.168.200.13:1521:pgi
   *  sqlServer jdbc:microsoft:sqlserver://192.168.200.61:1433;databaseName=demo | jdbc:sqlserver://192.168.200.14:1433;databaseName=cnse_ese
   **/
  private function decodeConnectionString($connectionStringArr) {
	$tmp="";
	foreach($connectionStringArr as $connectionString) /* processa ogni stringa di connessione */ {
	  $ar=explode(':', $connectionString);
	  if(is_array($ar) && sizeof($ar)>1) {
		switch ($ar[1]) {
		case "sybase":
		  $tmp.=$ar[1]." ".$ar[3]." ".$ar[4];
		  break;
		case "oracle":
		  $tmp.=$ar[1]." ".str_replace("@","",$ar[3])." ".$ar[4]." ".$ar[5]." ";
		  break;
		case "microsoft":
		  $tmp.=$ar[1]." ".str_replace("//","",$ar[3])." ".str_replace(";databaseName="," ",$ar[4]." ");
		  break;
		case "sqlserver":
		  $tmp.=$ar[1]." ".str_replace("//","",$ar[2])." ".str_replace(";databaseName="," ",$ar[3]." ");
		  break;
		}
	  }
	}
	return $tmp;
  }

  public function perform(IDTO $dto=null) {

	foreach($dto->getFeInstancesList() as $instances) {
	  if($instances->getExistsDeploy()) {
		$catalinaHome=$instances->getCatalinaHome();

		$instances->setBeInstanceArray($this->decodeConnectionString($this->getDatabaseRef($catalinaHome)));

	  }
	}

	return TRUE;
  }
}


/** Oracle specific commands **/

class QueryOratabFileCommand  extends AbstractCommand {

  const COMMAND_QUERY_ORATAB_FILE="if [ -f /etc/oratab ]; then cat /etc/oratab|grep -v '^#'; fi;";

  public function perform(IDTO $dto=null) {

	$tempArr=array_map("trim",explode(PHP_EOL,$this->serviceProvider->exec(self::COMMAND_QUERY_ORATAB_FILE)));
	$oracleSidArray = array_filter($tempArr,"notEmpty");

	foreach($oracleSidArray as $oratabEntry) {

	  $tmp=explode(":",$oratabEntry);

	  $sidName=$tmp[0];
	  $oracleHome=$tmp[1];
	  $runOnStartup=$tmp[2]=='Y'?1:0;

      $beInstance=new BackendInstanceEntity($sidName);
	  $beInstance->setOracleHome($oracleHome);
	  $beInstance->setRunningAtStartup($runOnStartup);
	  $dto->addInstance($beInstance);

	}

	return TRUE;
  }
}

class AreOracleInstancesRunningCommand extends AbstractCommand {

  private function isOracleInstanceRunning($instanceName,$oracleRunningProcessesArr) {
	return my_array_search("/".$instanceName."/",$oracleRunningProcessesArr);
  }

  /* Estrae l' elenco dei processi oracle in esecuzione */
  private function getOracleProcesses() {
	$oracleArray = array_filter(explode(PHP_EOL, $this->serviceProvider->exec("ps auxww|grep psp|grep -v grep")),"notEmpty");
	return array_map("trim",$oracleArray);
  }

  private function my_array_search($needle,$haystack) {
    foreach($haystack as $key=>$value) {
	  if(preg_match($needle,$haystack[$key])) {
		return 1;
	  }
    }
    return 0;
  }

  public function perform(IDTO $dto=null) {

	$oracleRunningProcessesArr=$this->getOracleProcesses(); /* estrae l' array con i processi oracle in esecuzione */

	foreach($dto->getBeInstancesList() as $instance) {
	  $instanceName=$instance->getInstanceName();
	  $isRunning=$this->isOracleInstanceRunning($instanceName,$oracleRunningProcessesArr);
	  $instance->setIsRunning($isRunning);
	}
	return TRUE;
  }
}


/** Proxy specific commands **/

// @TOOD : implmentare i comandi da eseguire sui proxy




