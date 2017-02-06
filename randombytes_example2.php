<?php

require('randombytes.class.php');

use CopySense\RandomBytes\RandomBytes;


$aData = RandomBytes::generate(32, 'openssl');

var_dump($aData);

?>