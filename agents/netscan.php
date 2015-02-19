<?php
use LSS\XML2Array;
$loop->addPeriodicTimer(1, function() {
  $export_file = "nmap." . date("Y-m-D_His") . ".xml";
  $ip_range = "10.0.0.1-255";
  exec("nmap -sn -oX {$export_file} {$ip_range}");
  $report_xml = file_get_contents($export_file);
  unlink($export_file);
  $network = XML2Array::createArray($report_xml);
  foreach($network['nmaprun']['host'] as $host){
    $ip = $host['address']['@attributes']['addr'];
    $hostname = isset($host['hostnames']['hostname']) ? $host['hostnames']['hostname']['@attributes']['name'] : "";
    echo "{$ip} {$hostname}\n";
  }

  exit;
});
