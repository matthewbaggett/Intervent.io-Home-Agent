<?php

function get_wifi_strength()
{
  $wifi = new \Intervent\HomeAgents\Sensors\Wifi();
  $wlan = $wifi->device("wlan0");
  if(count($wlan) == 1 && isset($wlan[""])){
    return false;
  }else{
    return [$wlan['signal level'], $wlan];
  }
}

$loop->addPeriodicTimer(1, function() use (&$prev_strength_db){
  list($strength_db, $payload) = get_wifi_strength();
  if($strength_db){
    if($strength_db != $prev_strength_db) {
      $delta_strength = abs($strength_db - $prev_strength_db);
      if($delta_strength > 5) {
        $prev_strength_db = $strength_db;
        echo "Updated Wifi Strength: {$strength_db} (delta {$delta_strength})\n";
        \Eventsd\Eventsd::trigger("WifiStrength", $payload);
      }
    }
  }
});
