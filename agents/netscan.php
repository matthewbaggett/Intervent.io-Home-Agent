<?php
use \LSS\XML2Array;
use \Intervent\HomeAgents\Models\NetworkLocation;

$loop->addPeriodicTimer(1, function() {
  $export_file = "nmap." . date("Y-m-D_His") . ".xml";
  // TODO: Decide what IP ranges to scan based on machine config. Maybe look into ifconfig.
  $ip_range = "10.0.0.1-255";
  exec("nmap -sn -oX {$export_file} {$ip_range}");
  $report_xml = file_get_contents($export_file);
  unlink($export_file);
  $network = XML2Array::createArray($report_xml);
  foreach($network['nmaprun']['host'] as $host){
    $ip = $host['address']['@attributes']['addr'];
    $hostname = isset($host['hostnames']['hostname']) ? $host['hostnames']['hostname']['@attributes']['name'] : "";
    echo "{$ip} {$hostname}\n";

    $is_new = false;
    $network_location = NetworkLocation::search()->where("ip", $ip)->execOne();
    if(!$network_location){
      // New network location found
      $network_location = new NetworkLocation();
      $is_new = true;
    }
    $network_location->ip = $ip;
    $network_location->hostname = $hostname;
    $network_location->last_seen = date("Y-m-d H:i:s");
    $network_location->save();

    if($is_new){
      \Eventsd\Eventsd::trigger("NetworkLocationFound", $network_location);
    }
  }

  exit;
});
