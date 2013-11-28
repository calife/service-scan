<?php

include '../util/CsvIterator.php';


$csvIterator = new CsvIteratorCipher((dirname(__FILE__)."/conf/authtest.csv"));

$count=0;

foreach ($csvIterator as $row => $data) {

  echo " >row: " . $row . " " . $data." < " . PHP_EOL;

  $num = count($data);
    for ($c=0; $c < $num; $c++) {
        echo $data[$c] . PHP_EOL;
    }

}
