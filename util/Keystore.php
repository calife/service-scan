<?php

require (dirname(__FILE__)."/Cipher.php");
require (dirname(__FILE__)."/Prompt.php");

/**
 *
 * Fornisce le primitive per l' accesso ai file cifrati
 *
 **/
class Keystore {


  protected static function askOverwrite($msg) {

	$confirmOverwrite=false;

	do {
	  $tmp = strtolower(Prompt::get($msg));
	} while($tmp!=='y' && $tmp!=='n');

	$confirmOverwrite=($tmp==='y')?true:false;

	return $confirmOverwrite;
  }

  /**
   * Crea un keystore a partire dall' arr
   **/
  public static function importFromArray($keystore,$arr=null,$passphrase=null) {


	$result=false;

	try {

		if(file_exists($keystore)) {

		  if(is_null($passphrase))
			$passphrase=Prompt::get("Enter passphrase to unlock keyring[".$keystore."]: ");

		  echo "before call verify";
		  if(TripleDesCipher::verify($keystore,$passphrase))
			echo ">>>>>>>>>>>OK".PHP_EOL;
		  else 			
			echo ">>>>>>>>>>>KO".PHP_EOL;

		  /* $fp = fopen($keystore, "w"); */
		  /* if (($bytes_written = @fwrite($fp, $crypteddata)) === false) { */
		  /* 	throw new Exception("Unable to write ".$keystore.PHP_EOL); */
		  /* } */
		  /* fclose($fp); */

		  $result=true;

		} else {
		  echo "$keystore does not exists.";
		  return false;
		}

	} catch (Exception $e) {
	  echo $e->getMessage();
	}

	/* echo "-----BEGIN CRIPTED DATA MESSAGE-----".PHP_EOL.$result.PHP_EOL."-----END CRIPTED DATA MESSAGE-----".PHP_EOL; */
	return $result;
  }

  /**
   * Crea un oggetto keystore importando il contenuto di $clearfile dopo averlo criptato
   **/
  public static function import($keystore,$clearfile) {
	$result=false;
	$confirmOverwrite=true;

	try {

	  if(!file_exists($clearfile) || !is_readable($clearfile) ) {

		throw new Exception("Unable to read file ".$clearfile.PHP_EOL);

	  } else {

		if(file_exists($keystore)) {

		  $confirmOverwrite = self::askOverwrite("Keystore ".$keystore." already exists. Overwrite[y/n]:");

		}

		if($confirmOverwrite) {

		  $passphrase=Prompt::getConfirmed("Enter passphrase to load file ".$clearfile." into the crypted keystore[".$keystore."]: ");

		  $data = @file_get_contents($clearfile, true);
		  if($data===FALSE) {
			throw new Exception("Error while reading file ".$clearfile.PHP_EOL);
		  }
		  $crypteddata=TripleDesCipher::encrypt($data,$passphrase);

		  $fp = fopen($keystore, "w");
		  if (($bytes_written = @fwrite($fp, $crypteddata)) === false) {
			throw new Exception("Unable to write ".$keystore.PHP_EOL);
		  }
		  fclose($fp);

		  $result=true;

		} else return false;

	  }

	} catch (Exception $e) {
	  echo $e->getMessage();
	}

	/* echo "-----BEGIN CRIPTED DATA MESSAGE-----".PHP_EOL.$result.PHP_EOL."-----END CRIPTED DATA MESSAGE-----".PHP_EOL; */
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
		throw new Exception("Unable to open keystore ".$keystore.PHP_EOL);
	  }

	  TripleDesCipher::decrypt($data,$passphrase);
	  $result = unlink($keystore);
      

	} catch (Exception $e) {
	  echo "Errore in fase di decifratura del keystore ".$keystore.PHP_EOL;
	  throw $e;
	}

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
	$confirmOverwrite=true;

	try {

	  if(file_exists($clearfile)) /* Chiedi la conferma per sovrascrivere */ {
		
		$confirmOverwrite = self::askOverwrite("Keystore ".$keystore." already exists. Overwrite[y/n]:");

	  }

	  if($confirmOverwrite) {

		$passphrase=Prompt::get("Enter passphrase to unlock keyring[".$keystore."]: ");

		$dataEncrypted = @file_get_contents($keystore, true);
		if($dataEncrypted===FALSE) {
		  throw new Exception("Unable to open keystore ".$keystore.PHP_EOL);
		}

		$data=TripleDesCipher::decrypt($dataEncrypted,$passphrase);

		$fp = @fopen($clearfile, "wb");

		if (($bytes_written = @fwrite($fp, $data)) === false) {
		  throw new Exception("Unable to write ".$clearfile.PHP_EOL);
		} else {
		  $result = true;
		}
		@fclose($fp);

	  } else return false;

	} catch (Exception $e) {
	  echo "Errore in fase di esportazione del keystore ".$keystore." sul file ".$clearfile.PHP_EOL;
	  throw $e;
	}

	return $result;
  }


  public static function changekeypasswd($keystore) {
	$result=false;
	$dataTemp;

	$passphrase=Prompt::get("Enter passphrase to unlock keyring[".$keystore."]: ");

	try {

	  /* Apre il keystore */
	  $dataEncrypted = @file_get_contents($keystore, true);
	  if($dataEncrypted===FALSE) {
		throw new Exception("Unable to open keystore ".$keystore.PHP_EOL);
	  }
	  $data=TripleDesCipher::decrypt($dataEncrypted,$passphrase);

	  /* Crea un file temporaneo con il contenuto in chiaro del keystore */
	  $handle = tmpfile();
      if(($numbytes = @fwrite($handle, $data))===false) {
		throw new Exception("Unable to write ".$handle.PHP_EOL);
      } else {
		@rewind($handle);
		$dataTemp = @stream_get_contents($handle);
		@fclose($handle);

		/* Crea un nuovo keystore con una nuova password ed il contento del $dataTemp */
		$passphrase=Prompt::getConfirmed("Enter new passphrase for the crypted keystore[".$keystore."]: ");

		$crypteddata=TripleDesCipher::encrypt($dataTemp,$passphrase);

		$fp = fopen($keystore, "w");
		if (($bytes_written = @fwrite($fp, $crypteddata)) === false) {
		  throw new Exception("Unable to write ".$keystore.PHP_EOL);
		}
		fclose($fp);

		$result = true;

	  }
	  
	} catch (Exception $e) {
	  echo "Errore in fase di decifratura del keystore ".$keystore.PHP_EOL;
	  @fclose($handle);
	  throw $e;
	}

	return $result;
  }


  /**
   * Apre il keystore, applica la funzione e richiude il keystore
   **/
  public static function executeFunction($keystore,$funz) {

	$result=false;

	try {

		$passphrase=Prompt::get("Enter passphrase to unlock keyring[".$keystore."]: ");

		$dataEncrypted = @file_get_contents($keystore, true);
		if($dataEncrypted===FALSE) {
		  throw new Exception("Unable to open keystore ".$keystore.PHP_EOL);
		}

		$data=TripleDesCipher::decrypt($dataEncrypted,$passphrase);

		// TODO: spostare exportCustom in funz
		$tempData=$funz(self::exportCustom($data));

		$crypteddata=TripleDesCipher::encrypt( self::arrayToString($tempData),$passphrase);

		$fp = fopen($keystore, "w");
		if (($bytes_written = @fwrite($fp, $crypteddata)) === false) {
		  throw new Exception("Unable to write ".$keystore.PHP_EOL);
		}
		fclose($fp);

		$result = true;

	} catch (Exception $e) {
	  echo "Errore in fase di esportazione del keystore ".$keystore." sul file ".$clearfile.PHP_EOL;
	  throw $e;
	}

	return $result;

  }

  public static function addentry($keystore,$entry) {

	$result=false;

	try {

		$passphrase=Prompt::get("Enter passphrase to unlock keyring[".$keystore."]: ");

		$dataEncrypted = @file_get_contents($keystore, true);
		if($dataEncrypted===FALSE) {
		  throw new Exception("Unable to open keystore ".$keystore.PHP_EOL);
		}

		$data=TripleDesCipher::decrypt($dataEncrypted,$passphrase);

		$tempData=self::arrayToString(self::exportCustom($data));

		$tempData=$tempData.$entry;

		$crypteddata=TripleDesCipher::encrypt($tempData,$passphrase);

		$fp = fopen($keystore, "w");
		if (($bytes_written = @fwrite($fp, $crypteddata)) === false) {
		  throw new Exception("Unable to write ".$keystore.PHP_EOL);
		}
		fclose($fp);
	  
	} catch (Exception $e) {
	  echo "Errore in fase di decifratura del keystore ".$keystore.PHP_EOL;
	  @fclose($handle);
	  throw $e;
	}

	return $result;
  }

  public static function removeentry($keystore,$entry_by_ip) {

	$result=false;

	try {

		$passphrase=Prompt::get("Enter passphrase to unlock keyring[".$keystore."]: ");

		$dataEncrypted = @file_get_contents($keystore, true);
		if($dataEncrypted===FALSE) {
		  throw new Exception("Unable to open keystore ".$keystore.PHP_EOL);
		}

		$data=TripleDesCipher::decrypt($dataEncrypted,$passphrase);

		$tempArray=self::exportCustom($data);

		$clearData;
		foreach($tempArray as $row) {

		  if($row['ip']!=$entry_by_ip)
			$clearData.=$row['ip'].",".$row['port'].",".$row['user'].",".$row['pass'].PHP_EOL;

		}

		$crypteddata=TripleDesCipher::encrypt($clearData,$passphrase);

		$fp = fopen($keystore, "w");
		if (($bytes_written = @fwrite($fp, $crypteddata)) === false) {
		  throw new Exception("Unable to write ".$keystore.PHP_EOL);
		}
		fclose($fp);
	  
	} catch (Exception $e) {
	  echo "Errore in fase di decifratura del keystore ".$keystore.PHP_EOL;
	  @fclose($handle);
	  throw $e;
	}

	return $result;
  }


  /**
   * Restituisce un array
   **/
  private static function exportCustom($clearData) {

    $paramsArray=array();
	$rows=explode(PHP_EOL, $clearData);
	foreach($rows as $row) {
	  $tmp=explode(',',$row);
	  $credential= new CredentialAccess($tmp[0],$tmp[1],$tmp[2],$tmp[3]);
	  $paramsArray[]=$credential->toArray();
	}

	return $paramsArray;
  }


  private static function arrayToString($rows=array()) {

	$clearData;

	foreach($rows as $row) {
	  $clearData.=$row['ip'].",".$row['port'].",".$row['user'].",".$row['pass'].PHP_EOL;
	}

	return $clearData;
  }

}