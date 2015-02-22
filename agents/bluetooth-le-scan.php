<?php

$loop->addPeriodicTimer(1, function() use (&$existing_devices) {
  $hci_log = APP_ROOT . "/hci.txt";
  $scan_delay = 3;
  $scan_command = "hcitool lescan > {$hci_log} & (sleep {$scan_delay}; killall -INT hcitool)";
  //echo "Scanning for bluetooth LE devices:\n";
  //echo " > {$scan_command}\n";
  exec($scan_command);
  sleep(1);
  $output = file_get_contents($hci_log);
  unlink($hci_log);
  $output = explode("\n", $output);
  unset($output[0]);
  $detected_devices = array();
  foreach($output as $scan_line){
    $scan_line = trim($scan_line);
    if(!empty($scan_line)) {
      list($mac, $name) = explode(" ", $scan_line, 2);
      #echo "Detected: {$mac} {$name}\n";
      if (!isset($detected_devices[$mac]) || $name != "(unknown)") {
        $detected_devices[$mac] = $name;
      }
    }
  }

  foreach($detected_devices as $detected_mac => $detected_name) {
    if(isset($existing_devices[$detected_mac])){
      // Already exists.
    }else{
      // New.
      echo "Bluetooth LE: Found new device: {$detected_mac}\n";
    }
  }

  if(count($existing_devices)) {
    foreach ($existing_devices as $existing_mac => $existing_name) {
      if (!isset($detected_devices[$existing_mac])) {
        // Missing
        echo "Bluetooth LE: Missing device: {$existing_mac}\n";
      }
    }
  }

  $existing_devices = $detected_devices;

});

