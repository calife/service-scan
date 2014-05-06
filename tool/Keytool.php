#!/usr/bin/php

<?php

include_once( dirname(__FILE__) . "/../object/logic/CredentialAccessManager.php" );

interface ICommandLineParser {

  static function help(); /* Stampa l' help */
  static function processCmdLine(array $options); /* Processa la command line */

}

class Keytool implements ICommandLineParser {

  public static function processCmdLine(array $options) /* command dispatcher */ {

	if (is_array($options) ) {
	  
	  if(isset($options['help'])) {	/* help */
		self::help(); exit;
	  }

	  switch ($options['action']) {

	  case 'create':				/* create */
		if ( isset($options['keystore']) && isset($options['clearfile']) ) {
		  AccessManager::create($options['keystore'],$options['clearfile']);
		  break;
		} else { self::help(); exit; }

	  case 'delete':				/* delete */
		if ( isset($options['keystore']) ) {
		  AccessManager::delete($options['keystore']);
		  break;
		} else { self::help(); exit; }

	  case 'show':				    /* show */
		if ( isset($options['keystore']) ) {
		  AccessManager::show($options['keystore']);
		  break;
		} else { self::help(); exit; }

	  case 'export':				/* export */
		if ( isset($options['keystore']) && isset($options['clearfile']) ) {
		  AccessManager::export($options['keystore'],$options['clearfile']);
		  break;
		} else { self::help(); exit; }

	  case 'changekeypasswd':		/* changekeypasswd */
		if ( isset($options['keystore']) ) {
		  AccessManager::changekeypasswd($options['keystore']);
		  break;
		} else { self::help(); exit; }

	  case 'addentry':				/* addentry */
		if ( isset($options['keystore']) && isset($options['entry']) ) {
		  AccessManager::addentry($options['keystore'],$options['entry']);
		  break;
		} else { self::help(); exit; }

	  case 'removeentry':			/* removeentry */
		if ( isset($options['keystore']) && isset($options['entry']) ) {
		  AccessManager::removeentry($options['keystore'],$options['entry']);
		  break;
		} else { self::help(); exit; }

	  case 'verify':				    /* verify */
		if ( isset($options['keystore']) ) {
		  AccessManager::verify($options['keystore']);
		  break;
		} else { self::help(); exit; }

	  case 'sort':				    /* sort */
		if ( isset($options['keystore']) ) {
		  AccessManager::sort($options['keystore']);
		  break;
		} else { self::help(); exit; }

	  case 'removedup':			   /* removedup */
		if ( isset($options['keystore']) ) {
		  AccessManager::removedup($options['keystore']);
		  break;
		} else { self::help(); exit; }

	  case 'help':				  /* help */
		self::help(); exit;

	  default:					  /* default */
		self::help(); exit;

	  }
	} else { die("Error while parsing arguments"); }
}

  static function help() /* -help */ {

	$helpStr = <<<EOT

  Utilizzo Keytool:

      --help
      --action create  --keystore <keystore> --clearfile <clearfile>
      --action delete  --keystore <keystore>
      --action show  --keystore <keystore>
      --action export  --keystore <keystore> --clearfile <clearfile>
      --action changekeypasswd   --keystore <keystore>
      --action addentry  --keystore <keystore> --entry <entry>
      --action removeentry  --keystore <keystore> --entry <entry>
      --action verify --keystore <keystore>
      --action sort --keystore <keystore>
      --action removedup --keystore <keystore>
      --action help

EOT;

	print $helpStr;

  }

}

$longopts  = array(
    "action:", // required
    "keystore:",
    "clearfile:",
    "entry:",
    "help"
);

$options = getopt(null,$longopts);
Keytool::processCmdLine($options);
