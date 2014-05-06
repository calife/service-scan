<?php

/**
 * Configurazione del package database
 **/

define('ORA_CONNECTION_TYPE_DEFAULT', 1);
define('ORA_CONNECTION_TYPE_PERSISTENT', 2);
define('ORA_CONNECTION_TYPE_NEW', 3);

define("CLIENT_IDENTIFIER","MPUCCI-PHPCLIENT");
define("MODULE_NAME","MODULE_PHP");

define("ALTER_NSL_DATE_FORMAT",' alter session set nls_date_format="YYYY-MM-DD HH24:MI:SS" ');
define("NLS_NUMERIC_CHARACTERS",".,");
define('CHARSET', 'UTF8');
