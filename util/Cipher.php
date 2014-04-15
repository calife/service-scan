<?php

interface ICipher {

  static function encrypt($plaintext,$textkey);

  static function decrypt($crypttext,$textkey);

  static function verify($crypttext,$textkey);

}

/**
 * Define a custom exception class
 */
class UnableToDecryptException extends Exception {
    public function __construct($message="Unable to decrypt..., please verify password.") {
        parent::__construct($message);
    }

    public function __toString() {
        return __CLASS__ . ": {$this->message}\n";
    }

    public function customFunction() {
        echo "A custom function for this type of exception\n";
    }
}

class TripleDesCipher implements ICipher {

    const CYPHER = MCRYPT_3DES;
    const MODE   = MCRYPT_MODE_CBC;
	private static $securekey;

	/**
	 * Funzione di codifica della stringa in chiaro, supporta la verifica tramite precalcolo md5 del plaintext
	 * $plaintext
	 * $textkey
	 **/
    public static function encrypt($plaintext,$textkey) {

	  self::$securekey = hash('sha256',$textkey,TRUE);

	  $td = mcrypt_module_open(self::CYPHER, '', self::MODE, '');
	  $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
	  mcrypt_generic_init($td, self::$securekey , $iv);
	  $md5=md5($plaintext);
	  $crypttext = mcrypt_generic($td, $plaintext.$md5);
	  mcrypt_generic_deinit($td);


	  return base64_encode($iv.$crypttext);
    }

	/**
	 * Funzione di decodifica della stringa codificata, supporta la verifica tramite postcalcolo md5 del plaintext
	 * $crypttext
	 * $textkey
	 **/
    public static function decrypt($crypttext,$textkey) {

	  self::$securekey = hash('sha256',$textkey,TRUE);

	  $crypttext = base64_decode($crypttext);

	  $plaintext = '';
	  $td = mcrypt_module_open(self::CYPHER, '', self::MODE, '');
	  $ivsize = mcrypt_enc_get_iv_size($td);
	  $iv = substr($crypttext, 0, $ivsize);
	  $crypttext = substr($crypttext, $ivsize);

	  if ($iv) {
		  
		  mcrypt_generic_init($td, self::$securekey, $iv);
		  $plaintext = rtrim(mdecrypt_generic($td, $crypttext), "\0\4");

		  $decrypted = substr($plaintext, 0, -32);
		  $hash = substr($plaintext, -32);

		  if($hash!==md5($decrypted))
			throw new UnableToDecryptException();

	  }
	  return trim($decrypted);
    }

	/**
	 * Funzione di verifica della stringa decodificata
	 * $crypttext
	 * $textkey
	 **/
	public static function verify($crypttext,$textkey) {

	  try {

		self::decrypt($crypttext,$textkey);

      } catch(Exception $exp) {
		return FALSE;
      }
	  return TRUE;
    }

}
