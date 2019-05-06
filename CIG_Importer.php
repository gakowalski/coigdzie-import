<?php

class CIG_Importer {
  protected $config;

  function __construct($config = null) {
    if ($config === null) {
      include 'config.php';
      $this->config = $config['CIG_Importer'];
    }
  }

  function get_filename($date = null) {
    if ($date !== null) {
      $filename = $date . '_' . $this->config['name'] . '.xml';
    } else {
      $filename = $this->config['name'] . '.xml';
    }
    return $filename;
  }

  function import($date = null) {
    $filename = $this->get_filename($date);
    $url = $this->config['base_url'] . $filename;

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

  function get_xml($date = null) {
    $filename = $this->get_filename($date);

    if (file_exists($filename) === false || (date("Ymd") > date("Ymd", filemtime($filename)))) {
      $this->import($date);
    }

    return file_get_contents($filename);
  }
}
