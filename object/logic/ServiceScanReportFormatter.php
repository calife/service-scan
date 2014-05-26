<?php

require_once( dirname(__FILE__) . "/../../conf/app.conf" ); /* settings relativi al timezone, locales, etc ... */

interface AbstractDTOFormatter {

  public static function toTxt(IDTO $dto);

  public static function toHTML($dtos);

  public static function toXml(IDTO $dto);

}

class ServiceScanReportFormatter implements AbstractDTOFormatter {

  const runningNow="#3ee893"; /* green */
  const notRunningNow="#f2bb24"; /* orange */
  const inactiveAndNoDeploy="#e8093d"; /* red */

  const FE="fe";
  const BE="be";

  protected static function printReportHeader($type) {

	$title=($type===self::FE?"eFM Frontend Report":"eFM Backend Report");

	$str="";
	$str.="<div style=\"width:90%;\" >";
	$str.="<p style=\" font-size:20px;font-weight:bold;color:".self::runningNow."\">".$title;
    $str.="<span style=\" \">   [";
    $str.="<a href=\"#\"
     onclick=\"
     function show(elem) {
      	elem.style.display='block';
     }

     function hide(elem) {
   		elem.style.display='none';
   	 }

     if(".$type."===".self::FE.") {
       hide(document.getElementById('".self::FE."'));
       show(document.getElementById('".self::BE."'));
     } else {
       hide(document.getElementById('".self::BE."'));
   	   show(document.getElementById('".self::FE."'));
     }
     return false; \" >click here</a></span>]
   ";
	$str.="</p>";
	$str.="</div>";
	return $str;
  }

  protected static function printLegend($type) {
	$str="";
	$str.="<div>";
	$str.="<table width=\"15%\" style=\"font-size:6pt;border:1px solid black;margin-bottom:20px; \">";
	$str.="<tr><th colspan=\"2\" align=\"center\">Legend</th></tr>";
	$str.="<tr><td align=\"right\" style=\"background-color:".self::runningNow."\">&nbsp;&nbsp;&nbsp;</td><td align=\"left\">Running now</td></tr>";
	$str.="<tr><td align=\"right\" style=\"background-color:".self::notRunningNow."\">&nbsp;&nbsp;&nbsp;</td><td align=\"left\">Not running now</td></tr>";
	$str.="<tr><td align=\"right\" style=\"background-color:".self::inactiveAndNoDeploy."\">&nbsp;&nbsp;&nbsp;</td><td align=\"left\">Not Valid</td></tr>";
	$str.="</table>";
	$str.="</div>";

	return $str;
  }

  protected static function printHTMLTableHeader($type) {
	$str="";
	$str.="<thead>";
	$str.="<tr>";
	$str.="<th>Network address</th>";
	$str.="<th>Hostname</th>";
	$str.="<th>Current Date</th>";	
	if($type===self::FE) {
	  $str.="<th>Instance Name</th>";
	  $str.="<th>Script</th>";
	  $str.="<th>Running now</th>";
	  $str.="<th>Archibus deploy</th>";
	  $str.="<th>Archibus deploy exists</th>";
	  $str.="<th>JVM property</th>";
	  $str.="<th>Port</th>";
	  $str.="<th>Backend</th>";
	} elseif($type===self::BE) {
	  $str.="<th>Instance Name</th>";
	  $str.="<th>Oracle Home</th>";
	  $str.="<th>Running at startup</th>";
	  $str.="<th>Running now</th>";
	}
	$str.="</tr>";
	$str.="</thead>";
	return $str;
  }

  protected static function printHTMLTableFooter() {

	$currentDate=date("F j, Y, g:i a");

	$str="";
	$str.="<hr/>";
	$str.='<div id="footer" >';
	$str.="<h5><font face=\"Helvetica, Arial, Sans Serif\" size=\"-1\"><b>";
	$str.="Report created on ".$currentDate."<br/>";
	$str.="Generated by <a href=\"http://www.efmnet.com/\">service-scan</a> v.".APP_VERSION." ";
	$str.="[i686-pc-linux-gnu]<br/>© 2008-2013 by <a href=\"mailto:pucci.marcello@efmnet.com\" title=\"Send email to Supporto Tecnico eFM\">Supporto Tecnico eFM</a>, built: ".$currentDate."<br/>";
	$str.="for all instances (i.e. without a filtering expression)";
	$str.="</h5>";
	$str.='</div>';

	return $str;

  }

  /**
   * Required: $instances e' la lista di tutte le istanze  di un solo tipo (fe|be)
   * Return: la stringa html formattata.
   **/
  private static function formatFe(array $instances,$networkAddress,$hostname,$currentDate) {


	$numinstances=count($instances);

	if($numinstances) {
	  $counter=1;

	  $str="";
	  $str.="<tbody>";
	  foreach($instances as $instance) {

		$rowColor;
		if($instance->isRunning())
		  $rowColor=self::runningNow;
		else if( ! $instance->getExistsDeploy())
		  $rowColor=self::inactiveAndNoDeploy;
		else $rowColor=self::notRunningNow;

		$str.="<tr style=\"background-color:$rowColor; \" height=\"40px\">";
		if($counter++==1) {
		  $str.="<td rowspan=\"$numinstances\" style=\"background-color:white;\"><b>".$networkAddress."</b></td>";
		  $str.="<td rowspan=\"$numinstances\" style=\"background-color:white;\"><b>".$hostname."</b></td>";
		  $str.="<td rowspan=\"$numinstances\" style=\"background-color:white;\"><b>".$currentDate."</b></td>";
		}
		$str.="<td>".trim($instance->getInstanceName())."</td>";
		$str.="<td>".trim($instance->getInitScript())."</td>";
		$str.="<td style=\"text-align:center;\">".($instance->isRunning()?" Y ":" N ")."</td>";
		$str.="<td>".$instance->getCatalinaHome()."</td>";
		$str.="<td style=\"text-align:center;\">".($instance->getExistsDeploy()?" Y ":" N ")."</td>";
		$str.="<td>";
		$str.="<p>".$instance->getJavaPropertyFromJavaCmdLine("Xms")."</p>";
		$str.="<p>".$instance->getJavaPropertyFromJavaCmdLine("Xmx")."</p>";
		$str.="<p>".$instance->getJavaPropertyFromJavaCmdLine("XX:PermSize")."</p>";
		$str.="<p>".$instance->getJavaPropertyFromJavaCmdLine("XX:MaxPermSize")."</p>";
		$str.="</td>";
		$str.="<td>".$instance->getTcpIpPortsArray()."&nbsp;</td>";
		$str.="<td>".$instance->getBeInstanceArray()."&nbsp;</td>";
		$str.="</tr>";
	  }
	  $str.="</tbody>";
		
	}

	return $str;

  }

  /**
   * Required: $instances e' la lista di tutte le istanze  di un solo tipo (fe|be)
   * Return: la stringa html formattata.
   **/
  private static function formatBe(array $instances,$networkAddress,$hostname,$currentDate) {

	$numinstances=count($instances);

	if($numinstances) {
	  $counter=1;

	  $str="";
	  $str.="<tbody>";
	  foreach($instances as $instance) {

		$rowColor;
		if($instance->isRunning())
		  $rowColor=self::runningNow;
		else $rowColor=self::notRunningNow;

		$str.="<tr style=\"background-color:$rowColor; \" height=\"40px\">";
		if($counter++==1) {
		  $str.="<td rowspan=\"$numinstances\" style=\"background-color:white;\"><b>".$networkAddress."</b></td>";
		  $str.="<td rowspan=\"$numinstances\" style=\"background-color:white;\"><b>".$hostname."</b></td>";
		  $str.="<td rowspan=\"$numinstances\" style=\"background-color:white;\"><b>".$currentDate."</b></td>";
		}
		$str.="<td>".trim($instance->getInstanceName())."</td>";
		$str.="<td>".trim($instance->getOracleHome())."</td>";
		$str.="<td style=\"text-align:center;\">".($instance->getRunningAtStartup()?" Y ":" N ")."</td>";
		$str.="<td style=\"text-align:center;\">".($instance->isRunning()?" Y ":" N ")."</td>";
		$str.="</tr>";
	  }
	  $str.="</tbody>";
		
	}

	return $str;
  }

  /**
   * Stampa il singolo HostDTO e tutte le istanze di Fe e Be che contiene
   **/
  protected static function printDTO(IDTO $dto,$type) {

	$str="";

	if($dto instanceof HostDTO) {

	  if($type===self::FE) {

		$feInstanceList=$dto->getFeInstancesList();
		if(count($feInstanceList))
		  $str.=self::formatFe($feInstanceList,$dto->getNetworkAddress(),$dto->getHostname(),$dto->getCurrentDate());

	  } elseif($type===self::BE) {

		$beInstanceList=$dto->getBeInstancesList();
		if(count($beInstanceList))
		  $str.=self::formatBe($beInstanceList,$dto->getNetworkAddress(),$dto->getHostname(),$dto->getCurrentDate());

	  }

	}

	return $str;
  }

  protected static function printHTMLTableBody($dtos,$type) {

	$str="";
	if(is_array($dtos)) /* se e' un array stampa tutti in dto contenuti */ {

	  foreach($dtos as $dto)
		$str.=self::printDTO($dto,$type);

	} else if($dtos instanceof HostDTO) /* altrimenti stampa il singolo dto */ {
	  $str.=self::printDTO($dtos,$type);
    }

	return $str;

  }

  public static function toHTML($dtos) {

	$str="";
	$str.='<html>';
	$str.='<head><title>Service Report</title></head>';
	$str.='<body vlink="blue" link="blue">';

	foreach(array(self::FE,self::BE) as $type) {

	  $initialVisibility=($type!==self::FE?"none":"block"); /* all' onload visualizza solo FE */

	  $str.='<div id="'.$type.'"  style=" display: '.$initialVisibility.'; padding-top:20px; padding-bottom:20px; min-height:580px;height:auto; " >';

	  $str.=self::printReportHeader($type);
	  $str.=self::printLegend($type);
	  $str.="<table border=\"1pt solid black\" style=\"font-size:6pt; margin-left:auto; margin-right:auto; \" >";
	  $str.=self::printHTMLTableHeader($type);
	  $str.=self::printHTMLTableBody($dtos,$type);
	  $str.="</table>";

	  $str.="</div>";


	}

	$str.=self::printHTMLTableFooter();
	$str.='</body>';
	$str.='</html>';
	return $str;
  }

  public static function generateReport(array $dtos) {
	$time_start = microtime(true);

    $report= self::toHTML($dtos);

	echo 'Report generated in ' . (microtime(true) - $time_start).' seconds '.PHP_EOL;
	return $report;
  }

  public static function toXml(IDTO $dto) {   }

  public static function toTxt(IDTO $dto) {   }


}
