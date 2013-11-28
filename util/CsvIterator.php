<?php

require (dirname(__FILE__)."/FileCipher.php");

/* Methods */
/* abstract public mixed current ( void ) */
/* abstract public scalar key ( void ) */
/* abstract public void next ( void ) */
/* abstract public void rewind ( void ) */
/* abstract public boolean valid ( void ) */

class CsvIterator implements Iterator {

    private $filePointer;
    private $currentElement;
    private $position;
    private $delimiter;

    public function __construct($file, $delimiter=',') {
        try {
		  $this->filePointer = fopen($file, 'r');
		  if ($this->filePointer===false) {
			throw new Exception("Error while reading the file ".$file);
		  }

		  $this->delimiter = $delimiter;
		  $this->position=0;
		  $this->currentElement=null;

        } catch (Exception $e) {
		  echo " Exception: ".$e->getMessage().PHP_EOL;
        }
    }

	public function __destruct() {
	  if($this->filePointer) {
		fclose($this->filePointer);
      }
    }

    public function rewind() {
	  $this->position = 0;
	  rewind($this->filePointer);
    }

    public function current() {
	  $this->currentElement = fgetcsv($this->filePointer, null, $this->delimiter);
	  $this->position++;
	  return $this->currentElement;
    }

    public function key() /* Returns the key of the current element. */ {
        return $this->position;
    }

    public function next() /* Moves the current position to the next element. */ {
	  return ($this->filePointer==TRUE) && !feof($this->filePointer);
    }

    public function valid() {
        if (!$this->next()) {
            fclose($this->filePointer);
            return false;
        }
        return true;
    }
}


class CsvIteratorCipher extends CsvIterator {

  public function __construct($keystore, $delimiter=',') {
  	parent::__construct($keystore, $delimiter);
  }

}
