<?php

/**
 * Configurazione del database driver
 **/

define('ORA_CONNECTION_TYPE_CONNECT', 1); /* The second and subsequent calls to oci_connect() 
                                             with the same parameters will return the connection 
                                             handle returned from the first call. 
                                             This method employ a connection cache. 
                                             The cache used by oci_connect() is cleaned up at the end 
                                             of the script run, or when the connection handle is explicitly closed. */

define('ORA_CONNECTION_TYPE_PCONNECT', 2); /* Persistent connections are cached and re-used between requests, 
                                              resulting in reduced overhead on each page load; 
                                              a typical PHP application will have a single persistent connection 
                                              open against an Oracle server per Apache child process.
                                              Effective use of persistent connections requires that PHP be an Apache module 
                                              or used with FGCI, or similar. 
                                              Persistent connections will not have any benefits over oci_connect() when PHP 
                                              is used with CGI or via the command-line. */

define('ORA_CONNECTION_TYPE_NEW_CONNECT', 3); /* This method creates a new connection to the Oracle server, 
                                                 regardless of what other connections might already exist */

define("CLIENT_IDENTIFIER","MPUCCI-PHPCLIENT");
define("MODULE_NAME","MODULE_PHP");

define("ALTER_NSL_DATE_FORMAT",' alter session set nls_date_format="YYYY-MM-DD HH24:MI:SS" ');
define("NLS_NUMERIC_CHARACTERS",".,");
define('CHARSET', 'UTF8');
