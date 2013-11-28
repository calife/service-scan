<?php

/**
 * Receiver class for the command pattern implementation.
 * <a href="http://en.wikipedia.org/wiki/Command_pattern">Command Pattern</a>
 **/
abstract class GenericServiceProvider {
  abstract public function exec($cmd=null);
}

class SSHServiceProvider extends GenericServiceProvider {

  private $connection;
  private $host;
  private $user;
  private $port;
  private $password;

  public function __construct() {
	if (!function_exists('ssh2_connect') || !function_exists('ssh2_exec') ) {
	  throw new Exception("ssh2 module not installed!");
	}
  }

  public function connect($host,$port=22,$user,$password) {
	$this->host = $host;
	$this->port = $port;
	$this->user = $user;
	$this->password = $password;
	$this->connection = @ssh2_connect($host, $port);
	try {
		$this->checkAuthenticationMethod() && $this->authenticate();
	} catch(Exception $e) {
	  echo $e->getMessage().PHP_EOL;
    }
  }

  private function checkAuthenticationMethod() {
	$auth_methods = @ssh2_auth_none($this->connection, $this->user);
	if (in_array('password', $auth_methods) or (in_array('keyboard-interactive', $auth_methods)) ) {
	  return TRUE;
	} else {
	  throw new Exception("Server does not supports password based nor keyboard-interactive authentication");
	}
  }

  private function authenticate() {
	if (isset($this->connection)) {
	  if (@ssh2_auth_password($this->connection, $this->user, $this->password)) {
	  } else {  throw new Exception("Failed to authenticate to host {$this->host}, wrong username or password."); }
	} else {  throw new Exception("Failed to authenticate to host {$this->host}, connection invalid."); }
  }

  /**
   * Ridefinizione del metodo astratto ereditato da GenericServiceProvider
   */
  public function exec($cmd=null) {
	if (!($stream = @ssh2_exec($this->connection, "bash -c '".$cmd."' ", FALSE))) {
	  throw new Exception("SSH command {$cmd} failed, {$this->host} ");
	}
	stream_set_blocking($stream, true);
	$data = "";
	while ($buf = fread($stream, 4096)) {
	  $data .= $buf;
	}
	fclose($stream);

	return trim($data);
  }

  public function disconnect() {
	$this->connection = null;
	unset($this->connection);
  }

  public function __destruct() {
	$this->disconnect();
  }

}