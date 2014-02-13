<?php

require_once( dirname(__FILE__) . "/../util/Keystore.php" );

/**
 * Tool di gestione del keystore
 * Permette le operazioni di import/export del keystore da riga di comando
 * 
 * Keytool Commands for Creating and Importing

 * Keytool Commands for Checking:

     Check which certificates are in a keystore
     keytool -list -v -keystore keystore.jks

 * Other Keytool Commands

     Delete a certificate from a Java Keytool keystore
        keytool -delete -alias "mydomain" -keystore keystore.jks

     Change a Java keystore password
        keytool -storepasswd -new new_storepass -keystore keystore.jks

     Export a certificate from a keystore
        keytool -export -alias mydomain -file mydomain.crt

 **/
class Keytool {

  //TODO: implementare il comando process per la gestione della CLI
  public function process(array $args=null) {

  }

}