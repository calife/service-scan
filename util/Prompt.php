<?php

set_time_limit(0);
@ob_end_flush();
ob_implicit_flush(true);

class Prompt {

  private static $tty;

  private static function setUp() {
    if (substr(PHP_OS, 0, 3) == "WIN") {
	  self::$tty = fopen("\con", "rb");
    } else {
      if (!(self::$tty = fopen("/dev/tty", "r"))) {
		self::$tty = fopen("php://stdin", "r");
      }
    }
  }

  public static function get($prompt, $length = 1024) {

	self::setUp();

    echo $prompt;

	if (substr(PHP_OS, 0, 3) !== "WIN") system('stty -echo');

    $result = trim(fGets(self::$tty, $length));

	if (substr(PHP_OS, 0, 3) !== "WIN") system('stty echo');

    echo PHP_EOL;
    return $result;
  }

  public static function getConfirmed($prompt, $length = 1024) {

	do {
	  $result=self::get($prompt, $length);
	  $resultConfirmed=self::get("Retype: ", $length);

	  if($result!==$resultConfirmed) {
		echo "Sorry, input does not match".PHP_EOL;
	  }
	} while($result!==$resultConfirmed);

	return $result;

  }

}

