<?php

define("QUERY_SYSDATE","select sysdate from dual");
define("QUERY_SYSDATE_TO_CHAR","select to_char (sysdate, 'YYYY-MM-DD HH24:MI:SS') as now from dual");
define("QUERY_SYSTIMESTAMP","select systimestamp from dual");
define("QUERY_USER","select user from dual");

define("QUERY_PHPINS_HOSTS","select * from phpins.hosts");
define("QUERY_PHPINS_HOSTS_NEXT_SEQUENCE","SELECT HOSTS_S.NEXTVAL FROM DUAL");
define("QUERY_PHPINS_HOSTS_CURRENT_SEQUENCE","SELECT HOSTS_S.CURRVAL FROM DUAL");


