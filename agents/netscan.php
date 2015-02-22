<?php
use \LSS\XML2Array;
use \Intervent\HomeAgents\Models\NetworkLocation;

$loop->addPeriodicTimer(60, function() {
  $export_file = "nmap." . date("Y-m-D_His") . ".xml";
  // TODO: Decide what IP ranges to scan based on machine config. Maybe look into ifconfig.
  $ip_range = "10.0.0.1-255";
  exec("nmap -sn -oX {$export_file} {$ip_range}");
  $report_xml = file_get_contents($export_file);
  unlink($export_file);
  $network = XML2Array::createArray($report_xml);
  $expected_locations = NetworkLocation::search()->where('last_seen', date("Y-m-d H:i:s", time() - 90), ">")->exec();

  if(count($expected_locations) > 0){
    foreach($expected_locations as $expected_location){
      /* @var $expected_location NetworkLocation */
      $exists = false;
      foreach($network['nmaprun']['host'] as $host){
        if($host['address']['@attributes']['addr'] == $expected_location->ip){
          $exists = true;
        }
      }
      if(!$exists){
        echo "Netscan: Lost Network Location: {$expected_location->ip}\n";
        \Eventsd\Eventsd::trigger("NetworkLocationLost", $expected_location);
      }
    }
  }
  foreach($network['nmaprun']['host'] as $host){
    $ip = $host['address']['@attributes']['addr'];
    $hostname = isset($host['hostnames']['hostname']) ? $host['hostnames']['hostname']['@attributes']['name'] : "";

    $is_new = false;
    $is_returning = false;
    $network_location = NetworkLocation::search()
      ->where("ip", $ip)
      ->where('last_seen', date("Y-m-d H:i:s", time() - 90), ">")
      ->execOne();
    if(!$network_location){
      $network_location = NetworkLocation::search()
        ->where("ip", $ip)
        ->execOne();
      $is_returning = true;
    }
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
      echo "Netscan: New Network Location: {$network_location->ip}\n";
      \Eventsd\Eventsd::trigger("NetworkLocationFound", $network_location);
    }
    if($is_returning){
      echo "Netscan: Returning Network Location: {$network_location->ip}\n";
      \Eventsd\Eventsd::trigger("NetworkLocationRejoined", $network_location);
    }
  }
});
