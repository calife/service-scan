<?php

include '../util/Cipher.php';
include '../util/Prompt.php';

$passphrase3=Prompt::get("Enter passphrase to decript: ");
$strcifrata=TripleDesCipher::encrypt("Good morning people!!!!!!!!",$passphrase3);
echo "Testo cifrato: ".$strcifrata.PHP_EOL;

$passphrase4=Prompt::get("Enter passphrase to decript: ");
$strdecifrata=TripleDesCipher::decrypt($strcifrata,$passphrase4);
echo "Testo decrifrato: ".$strdecifrata.PHP_EOL;




