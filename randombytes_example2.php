<?php

require('classes/randombytes.class.php');

use Tinram\RandomBytes\RandomBytes;

$aData = RandomBytes::generate(32, 'openssl');

var_dump($aData);