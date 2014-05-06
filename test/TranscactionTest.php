<?php
 
$starttime = microtime(TRUE);

$conn = oci_connect('phpins', '1234efm', 'localhost/XE');

$hostname="PUCCI";
$networkaddress="172.16.1.62";
$currentdate="";

$stid = oci_parse($conn, 'insert into phpins.hosts(host_name, network_address, current_date) values ( :host_name, :network_address, :current_date)');
oci_bind_by_name($stid, ':host_name', $hostname);
oci_bind_by_name($stid, ':network_address', $networkaddress);
oci_bind_by_name($stid, ':current_date', $currentdate);

oci_execute($stid, OCI_NO_AUTO_COMMIT);

oci_commit($conn);

$endtime = microtime(TRUE) - $starttime;

echo "Time elapsed was ".round($endtime,3)." seconds<br>";