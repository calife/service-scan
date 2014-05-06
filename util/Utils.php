<?php

function checkRequiredLibrary($funcName) {
  if (!function_exists($funcName))
    die("function $funcName doesn't exist");
}

function notEmpty($val) {
  return $val!=="";
}



function arrayPrint(array $arr) {
  if(count($arr)===0) {
	echo "No instance running".PHP_EOL;
  } else for ($i = 0; $i < count($arr); $i++) {
	  echo " " . $i . " " .$arr[$i] . " ".PHP_EOL;
	}
}

function my_array_search($needle,$haystack) {
    foreach($haystack as $key=>$value) {
		if(preg_match($needle,$haystack[$key])) {
		  return 1;
        }
    }
    return 0;
}


function ipcompare($ip1,$ip2) {
  return ip2long($ip1)>ip2long($ip2);
}



/**
 
  	Sort by Ip
	Pre:
 	$tmp= array(
	  array("ip"=>"127.0.0.1","port"=>22,"user"=>"user1","pass"=>"xxxxxx"),
	  array("ip"=>"192.168.200.16","port"=>22,"user"=>"user1","pass"=>"xxxxxx"),
	  array("ip"=>"192.168.200.12","port"=>22,"user"=>"user1","pass"=>"xxxxxx"),
	  array("ip"=>"10.10.203.88","port"=>22,"user"=>"user1","pass"=>"xxxxxxx")
	  );
	Post: Array $tmp ordinato sul campo 'ip'
	
**/
function my_sort_array_by_ip($tmp) {

  usort($tmp, function($a,$b) {
	  return ipcompare($a['ip'],$b['ip']);
	});

  return $tmp;
}

function my_unique_array_by_ip($in) {

  $tmp=array();
  foreach ($in as $row)
    if (!in_array($row,$tmp)) 
	  array_push($tmp,$row);

  return $tmp;
}


function my_find_dup_by_ip($in) {

  $exists = array();
  $duplicates = array();

  foreach ($in as $row) {
	if (in_array($row,$exists) &&  !in_array($row,$duplicates)) {
	  $duplicates[] = $row;
	} else {
	  $exists[] = $row;
	}
  }

  return $duplicates;

}