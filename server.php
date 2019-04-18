<?php

if (isset($webservice)) {
  ini_set('soap.wsdl_cache_enabled', 0);
  $server = new SoapServer("$webservice.wsdl");
  $server->setClass($webservice);

  if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD']=='POST') {
    $server->handle();
  } else {
    if (isset($_SERVER['QUERY_STRING']) && strcasecmp($_SERVER['QUERY_STRING'], 'wsdl') == 0) {
      $wsdl = @implode('', @file("$webservice.wsdl"));
      if (strlen($wsdl) > 1) {
        header("Content-type: text/xml");
        echo $wsdl;
      } else {
        header("Status: 500 Internal Server Error");
        header("Content-type: text/plain");
        echo "HTTP/1.0 500 Internal Server Error";
      }
    } else {
      /* ? */
    }
  }
} else {
  /* ? */
}
