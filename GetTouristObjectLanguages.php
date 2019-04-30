<?php

class GetTouristObjectLanguages {
  public function getLanguages($request) {
    $response = new \stdClass;
    $response->language = array('pl-PL');
    return $response;
  }
}

$webservice = 'GetTouristObjectLanguages';
require 'server.php';
