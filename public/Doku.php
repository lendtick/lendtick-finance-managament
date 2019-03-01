<?php

if (!function_exists('curl_init')) {
  throw new Exception('CURL PHP extension missing!');
}
if (!function_exists('json_decode')) {
  throw new Exception('JSON PHP extension missing!');
}

require('Core/Initiate.php');
require('Core/Api.php');
require('Core/Library.php');
?>