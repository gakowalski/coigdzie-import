<?php

class CIG_Importer {
  protected $config;

  function __construct($config = null) {
    if ($config === null) {
      include 'config.php';
      $this->config = $config['CIG_Importer'];
    }
  }

  function import($date = null) {
    $url = $this->config['base_url'] . $this->config['name'] . '.xml';
    $filename = $this->config['name'] . '.xml';

    $headers = array(
      'Authorization: Basic ' . base64_encode($this->config['login'] . ':' . $this->config['pass']),
    );

    $options = array(
      CURLOPT_FILE    => fopen($filename, 'w'),
      CURLOPT_TIMEOUT =>  120,
      CURLOPT_URL     => $url,
      CURLOPT_HTTPHEADER  => $headers,
    );

    $ch = curl_init();
    curl_setopt_array($ch, $options);
    curl_exec($ch);
    curl_close($ch);
  }
}
