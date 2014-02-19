<?php

require (dirname(__FILE__)."/Cipher.php");
require (dirname(__FILE__)."/Prompt.php");

/**
 *
 * Fornisce le primitive per l' accesso ai file cifrati
 *
 **/
class Keystore {


  /**
   * Crea un oggetto keystore importando il contenuto di $clearfile dopo averlo criptato
   **/
  public static function import($keystore,$clearfile) {
	$result=false;

	$passphrase=Prompt::getConfirmed("Enter passphrase to load file ".$clearfile." into the crypted keystore[".$keystore."]: ");

	try {
	  $data = @file_get_contents($clearfile, true);
	  if($data===FALSE) {
		throw new Exception("Unable to open file ".$clearfile.PHP_EOL);
	  }
	  $crypteddata=TripleDesCipher::encrypt($data,$passphrase);

	  $fp = fopen($keystore, "w");
	  if (($bytes_written = fwrite($fp, $crypteddata)) === false) {
		throw new Exception("Unable to write ".$keystore.PHP_EOL);
	  }
	  fclose($fp);

	  $result=true;

	} catch (Exception $e) {
	  echo "Errore in fase di decifratura del keystore ".$keystore.PHP_EOL;
	  throw $e;
	}

	/* echo "-----BEGIN CRIPTED DATA MESSAGE-----".PHP_EOL.$result.PHP_EOL."-----END CRIPTED DATA MESSAGE-----".PHP_EOL; */
	return $result;

  }

  /**
   * Decripta ed esporta il contenuto del keystore su un file di testo
   *
   * @TODO file_put_contents() will cause concurrency problems - that is, it doesn't write files atomically (in a single operation)
   *       http://it2.php.net/manual/it/function.file-put-contents.php#82934
   *
   **/
  public static function export($keystore,$clearfile) {

	$result=false;

	try {
	  if(file_exists($clearfile)) {
		throw new Exception("Destination file ".$clearfile." already exists.".PHP_EOL);
	  }	else {

		$fp = @fopen($clearfile, "wb");
		$data=self::show($keystore);
		if (($bytes_written = @fwrite($fp, $data)) === false) {
		  throw new Exception("Unable to write ".$clearfile.PHP_EOL);
		} else {
		  $result=TRUE;
		}
		@fclose($fp);

      }

	} catch (Exception $e) {
	  echo "Errore in fase di esportazione del keystore ".$keystore." sul file ".$clearfile.PHP_EOL;
	  throw $e;
	}

	return $result;
  }


  /**
   * Restituisce una stringa con il contenuto del keystore dopo averlo decriptato
   **/
  public static function show($keystore) {
	$result;

	$passphrase=Prompt::get("Enter passphrase to unlock keyring[".$keystore."]: ");

	try {
	  $data = @file_get_contents($keystore, true);
	  if($data===FALSE) {
		throw new Exception("Unable to open keystore ".$keystore.PHP_EOL);
	  }

	  $result=TripleDesCipher::decrypt($data,$passphrase);

	} catch (Exception $e) {
	  echo "Errore in fase di decifratura del keystore ".$keystore.PHP_EOL;
	  throw $e;
	}

	/* echo "-----BEGIN CLEAR DATA MESSAGE-----".PHP_EOL.$result.PHP_EOL."-----END CLEAR DATA MESSAGE-----".PHP_EOL; */
	return $result;
  }


  /**
   * Cancella un keystore dopo aver verificato che la password fornita sia valida
   **/
  public static function delete($keystore) {
	$result;

	$passphrase=Prompt::get("Enter passphrase to unlock keyring[".$keystore."]: ");

	try {

	  $data = @file_get_contents($keystore, true);

	  if($data===FALSE) {
		throw new Exception("Impossibile trovare il keystore ".$keystore.PHP_EOL);
	  } else {  
        TripleDesCipher::decrypt($data,$passphrase);
		$result = unlink($keystore);
      }

	} catch (Exception $e) {
	  echo "Errore in fase di decifratura del keystore ".$keystore.PHP_EOL;
	  throw $e;
	}

	return $result;
  }


}