<?php

$loop->addPeriodicTimer(1, function() use (&$existing_devices) {

  $lockfile = "/tmp/bluetoothlescan.lock";
  if(file_exists($lockfile) && file_get_contents($lockfile) == getmypid()){
    echo "Bluetooth LE: Skipping scan, scan already running\n";
    return;
  }
  file_put_contents($lockfile, getmypid());
  $hci_log = "/tmp" . "/hci.txt";
  $scan_delay = 5;
  $scan_command = "hcitool lescan > {$hci_log} & (sleep {$scan_delay}; killall -INT hcitool)";
  echo "Scanning for bluetooth LE devices:\n";
  echo " > {$scan_command}\n";
  exec($scan_command);
  sleep(1);
  if(!file_exists($hci_log)){
    echo "Bluetooth LE: HCI log missing or failed to generate.\n";
    unlink($lockfile);
    return;
  }
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
      \Eventsd\Eventsd::trigger("BluetoothLEFound", ["mac" => $detected_mac, "name" => $detected_name]);
      \Eventsd\Eventsd::trigger("lights-on", []);
      \Eventsd\Eventsd::trigger("lights-white", []);
    }
  }

  if(count($existing_devices)) {
    foreach ($existing_devices as $existing_mac => $existing_name) {
      if (!isset($detected_devices[$existing_mac])) {
        // Missing
        echo "Bluetooth LE: Missing device: {$existing_mac}\n";
        \Eventsd\Eventsd::trigger("BluetoothLELost", ["mac" => $existing_mac, "name" => $existing_name]);
      }
    }
  }

  $existing_devices = $detected_devices;
  unlink($lockfile);

});