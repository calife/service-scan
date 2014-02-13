<?php

require (dirname(__FILE__)."/Cipher.php");
require (dirname(__FILE__)."/Prompt.php");

class Keystore {

  private $keystore=null;

  public function __construct($keystore=null) {
	$this->keystore=$keystore;
  }

  /**
   * Crea un oggetto keystore importando il contenuto di $clearfile dopo averlo criptato
   **/
  public function importKeystore($clearfile) {
	$result;

	$passphrase=Prompt::getConfirmed("Enter passphrase to load file ".$clearfile." into the crypted keystore[".$this->keystore."]: ");

	try {
	  $data = @file_get_contents($clearfile, true);
	  if($data===FALSE) {
		throw new Exception("Unable to open file ".$clearfile.PHP_EOL);
	  }
	  $crypteddata=TripleDesCipher::encrypt($data,$passphrase);

	  $fp = fopen($this->keystore, "w");
	  if (($bytes_written = fwrite($fp, $crypteddata)) === false) {
		throw new Exception("Unable to write ".$this->keystore.PHP_EOL);
	  }
	  fclose($fp);

	} catch (Exception $e) {
	  throw $e;
	}

	/* echo "-----BEGIN CRIPTED DATA MESSAGE-----".PHP_EOL.$result.PHP_EOL."-----END CRIPTED DATA MESSAGE-----".PHP_EOL; */
	return $result;

  }

  /**
   * Restituisce il contenuto del keystore dopo averlo decriptato
   **/
  public function exportKeystore() {
	$result;

	$passphrase=Prompt::get("Enter passphrase to unlock keyring[".$this->keystore."]: ");

	try {
	  $data = @file_get_contents($this->keystore, true);
	  if($data===FALSE) {
		throw new Exception("Unable to open keystore ".$this->keystore.PHP_EOL);
	  }

	  $result=TripleDesCipher::decrypt($data,$passphrase);

	} catch (Exception $e) {
	  throw $e;
	}

	/* echo "-----BEGIN CLEAR DATA MESSAGE-----".PHP_EOL.$result.PHP_EOL."-----END CLEAR DATA MESSAGE-----".PHP_EOL; */
	return $result;
  }

}