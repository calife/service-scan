<?php
 
require(dirname(__FILE__)."/../object/database/ConnectionManager.php"); /* classe principale di connessione al database */
require(dirname(__FILE__)."/conf/database.conf"); /* credenziali di accesso al database */
require(dirname(__FILE__)."/conf/query.inc"); /* SQL stmt */

$starttime = microtime(TRUE);

$db = new ConnectionManager();

$db->connect(CONNECTION_STRING , ORA_CON_USERNAME , ORA_CON_PW , ORA_CONNECTION_TYPE_DEFAULT);

$db->setAutoCommit(FALSE);

$success=$db->executeSQL(" delete from phpins.hosts");
print_r("Cancellazione effettuata ".($success?"TRUE":"FALSE").PHP_EOL);
$db->commit();

$parametri = array(':host_name' => '________________PUCCI',':network_address' => '172.16.1.62',':current_date' => 'aaaaaa');
$success=$db->executeSQL(" insert into phpins.hosts(host_name, network_address, current_date) values ( :host_name, :network_address, :current_date) ",$parametri);
$db->commit();

print_r("Inserimento effettuato ".($success?"TRUE":"FALSE").PHP_EOL);


$current_id=$db->getCurrentValueFromSequence("HOSTS_S");
echo "Valore corrente ".$current_id.PHP_EOL;

$next_id=$db->getNextValueFromSequence("HOSTS_S");
echo "Prossimo valore ".$next_id.PHP_EOL;


$endtime = microtime(TRUE) - $starttime;
echo "Time elapsed was ".round($endtime,3)." seconds<br>";