<?php

$response = array(
  'new' => array(),
  'unchanged' => array(),
  'changed' => array(),
  'deleted' => array(),
);

if (isset($_GET['ids'])) {
  require 'CIG_Importer.php';

  $importer = new CIG_Importer();
  $happenings = new SimpleXMLElement($importer->get_xml());

  $latest = array();

  if (isset($_GET['from'])) {
    $from = date("Ymd", strtotime($_GET['from']));
    $changed = array();

    foreach ($happenings as $happening) {
      if ($happening['deleted'] == '1') {
        continue;
      }
      $latest[] = strval($happening['happeningId']);

      if ($from <= date("Ymd", strtotime($happening['modified']))) {
        $changed[] = strval($happening['happeningId']);
      }
    }

    $importer->import($from);
    $happenings = new SimpleXMLElement($importer->get_xml($from));

    $old = array();

    foreach ($happenings as $happening) {
      if ($happening['deleted'] == '1') {
        continue;
      }
      $old[] = strval($happening['happeningId']);
    }

    $response['ignore'] = array_values(array_intersect($old, $latest));
    $response['new'] = array_values(array_diff($latest, $response['ignore']));
    $response['deleted'] = array_values(array_diff($old, $response['ignore']));
    $response['unchanged'] = array_values(array_diff($response['ignore'], $changed));
    $response['changed'] = array_values(array_diff($response['ignore'], $response['unchanged']));
    unset($response['ignore']);
  } else {

    foreach ($happenings as $happening) {
      $latest[] = strval($happening['happeningId']);
    }

    $response['new'] = $latest;
  }

}

header('Access-Control-Allow-Origin: *');
header('Content-type:application/json;charset=utf-8');
echo json_encode($response);
