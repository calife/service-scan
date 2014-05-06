<?php
 
/**
 * ConnectionManager.php: Database class using the PHP OCI8 extension
 */

include_once( dirname(__FILE__) . "/conf/settings.inc" ); /* settings relativi al timezone, locales, etc ... */

error_reporting(E_ALL|E_STRICT);

/**
 * Oracle Database access methods
 */
class ConnectionManager {
 
    protected $conn = null; /* The connection resource */
    protected $stmtid = null; /* The statement resource identifier */
    protected $prefetch = 100; /* The number of rows to prefetch with queries */

	protected $autoCommit;
	protected $internalDebug;
	protected $clientIdentifier;
	protected $moduleName;
	protected $charset;

	/**
     * Constructor Does NOT opens a connection to the database!!!
     */
    function __construct($autoCommit = FALSE , $internalDebug = TRUE , $clientIdentifier = CLIENT_IDENTIFIER , $moduleName = MODULE_NAME, $charset = CHARSET) {

	  $this->autoCommit = $autoCommit;
	  $this->internalDebug = $internalDebug;
	  $this->moduleName = $moduleName;
	  $this->clientIdentifier = $clientIdentifier;
	  $this->charset = $charset;

    }

	/**
	 * Open a connection to the database
    * @param string $host
    * @param string $user
    * @param string $pass
    * @param int $type (ORA_CONNECTION_TYPE_DEFAULT, ORA_CONNECTION_TYPE_NEW, ORA_CONNECTION_TYPE_PERSISTENT)
    * @return bool
	 */
	/* function connect($host = CONNECTION_STRING , $user = ORA_CON_USERNAME , $pass = ORA_CON_PW , $type = ORA_CONNECTION_TYPE_DEFAULT ) { */
	function connect($host , $user , $pass , $type ) {

      switch ($type) {
          case ORA_CONNECTION_TYPE_PERSISTENT:
			$this->conn = @oci_pconnect($user, $pass, $host, $this->charset); 
			break;
          case ORA_CONNECTION_TYPE_NEW:
			$this->conn = @oci_new_connect($user, $pass, $host, $this->charset); 
			break;
          default: 
              $this->conn = @oci_connect($user, $pass, $host, $this->charset);
      }   

	  if (is_resource($this->conn)) {
		oci_set_module_name($this->conn,$this->moduleName);
		oci_set_client_info($this->conn,$this->clientIdentifier);
		oci_set_client_identifier($this->conn,$this->clientIdentifier);
	  } else {
		$e = oci_error();
		trigger_error('Could not connect to database: '. $e['message'],E_USER_ERROR);
      }

	  return is_resource($this->conn) ? true : false;

    }

	private function parseStmt($sql) {

	  $this->stmtid = @oci_parse($this->conn, $sql);

	  if (!is_resource($this->stmtid)) {
		$e = oci_error();
		trigger_error('Could not parse statement: '. $e['message'], E_USER_ERROR);
	  }
    }

	private function bindParameters($params = array()) {
	  foreach ($params as $key => $val) /* prepare the statement*/ {
		@oci_bind_by_name($this->stmtid, $key, $params[$key],-1);
	  }
    }

	/**
	 * Return TRUE in caso di successo, FALSE altrimenti
	 **/
	private function executeStmt($mode) {
	  return oci_execute($this->stmtid,$mode);
    }

	/**
     * Run a SQL or PL/SQL statement
     * @param string $sql The Prepared Statement to run
     * @param array $params Binds. An array of (bv_name, php_variable)
	 * Return: TRUE in caso di successo, FALSE altrimenti
     */
    public function executeSQL($sql, $params = array()) {

	  $this->parseStmt($sql);

	  $this->bindParameters($params);

	  if ($this->prefetch >= 0) /* set prefetch size */ {
		oci_set_prefetch($this->stmtid, $this->prefetch);
	  }

	  $commit_mode = $this->autoCommit ? OCI_COMMIT_ON_SUCCESS : OCI_DEFAULT;
	 
	  return $this->executeStmt($commit_mode);

    }

	/**
     * Run a query and return all rows.
     * The bind lengths are set to -1 telling PHP to infer internal buffer sizes from the lengths of the PHP values.
     * @param string $sql A query to run and return all rows
     * @param array $params Binds.
     * @return array An array of rows
     */
    public function selectAll($sql,$params=array()) {
	  $this->executeSQL($sql,$params);
	  @oci_fetch_all($this->stmtid, $rows, 0, -1, OCI_FETCHSTATEMENT_BY_ROW);
	  return $rows;
    }

    /**
    * Gets the number of columns in the given statement. 
    * 
    * @param resource $statement
    * @return int
    */
    public function getFieldsColumn($sql, $params = array()) {

	  $this->parseStmt($sql);

	  $this->bindParameters($params);

	  $this->executeStmt(OCI_DESCRIBE_ONLY);

	  return oci_num_fields($this->stmtid);

    }

    /**
    * Gets the number of rows affected during statement execution.
    * @param resource $statement
    * @return int
    */
    public function getNumRows($sql, $params = array()) {

	  $this->parseStmt($sql);

	  $this->bindParameters($params);

	  $this->executeStmt(OCI_DESCRIBE_ONLY);

	  @oci_fetch_all($this->stmtid, $rows, 0, -1, OCI_FETCHSTATEMENT_BY_ROW);

	  return oci_num_rows($this->stmtid);
    }

    /**
    * Commit transaction
    * @return bool
    */
    public function commit() {
        if (is_resource($this->conn))
          return @oci_commit($this->conn);
        else 
          return false;
    }  

    /**
    * Rollback transaction
    * @return bool
    */
    public function rollback() {
        if (is_resource($this->conn))
          return @oci_rollback($this->conn);
        else 
          return false;
    }  

    public function setInternalDebug($onoff) {
        oci_internal_debug($onoff);
    }

    public function getInternalDebug() {
	  return $this->internalDebug;
    }

	public function setAutoCommit($mode = true) {
        $this->autoCommit = $mode;
    }

	public function getAutoCommit() {
	  return $this->autoCommit;
    }

	public function setClientIdentifier($clientIdentifier) {
        $this->clientIdentier = $clientIdentifier;
    }

	public function getClientIdentifier() {
	  return $this->clientIdentifier;
    }

	public function setModuleName($moduleName) {
        $this->moduleName = $moduleName;
    }

	public function getModuleName() {
	  return $this->moduleName;
    }

	public function setCharset($charset) {
        $this->charset = $charset;
    }

	public function getCharset() {
	  return $this->charset;
    }

    public function getOracleVersion(){
        if (is_resource($this->conn))
          return @oci_server_version($this->conn);
        else 
          return false;
    }

	/**
	 * Legge il valore corrente della sequence $sequenceName senza incrementare
	 **/
	public function getCurrentValueFromSequence($sequenceName) {
	  return $this->readOracleSequence($sequenceName,"current");
    }

	/**
	 * Legge il valore corrente della sequence $sequenceName incrementandola
	 **/
	public function getNextValueFromSequence($sequenceName) {
	  return $this->readOracleSequence($sequenceName,"next");
    }

	private function readOracleSequence($sequenceName=null,$currentOrNext="current") {

	  $id="0";

	  if($sequenceName) {
		if($currentOrNext==="current") {
		  $result=$this->selectAll(" select ".$sequenceName.".CURRVAL from dual ");
		  $id=$result[0]['CURRVAL'];
		} else {
		  $result=$this->selectAll(" select ".$sequenceName.".NEXTVAL from dual ");
		  $id=$result[0]['NEXTVAL'];
		}
	  }

	  return intval($id);
    }

    /**
     * Destructor closes the statement and connection
     */
    function __destruct() {

	  if (is_resource($this->stmtid))
		  oci_free_statement($this->stmtid);

	  if (is_resource($this->conn))
		  oci_close($this->conn);
    }

	public function __toString() {
	  print_r($this->conn);
	  return "_____";
    }

}
