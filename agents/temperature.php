<?php

function get_system_temperature()
{
  if (file_exists("/opt/vc/bin/vcgencmd")) {
    exec("/opt/vc/bin/vcgencmd measure_temp", $temperature);

    if(substr($temperature[0],0,5) == "temp=") {
      return str_replace("'C", "", str_replace("temp=", "", $temperature[0]));
    }else{
      return false;
    }
  }
  if (file_exists("/usr/bin/sensors")) {
    $lm_sensors = new Intervent\HomeAgents\Sensors\LMSensors();

    $lm_sensor_data = $lm_sensors->getMBInfo();
    $temperature = $lm_sensor_data->getMbTemp();

    return $temperature[0]->getValue();
  }
  return false;
}

$loop->addPeriodicTimer(30, function() use (&$current_temperature){
  // Get raspberry pi CPU temperature

  $new_temperature = get_system_temperature();
  if($new_temperature){
    if($current_temperature != $new_temperature) {
      $current_temperature = $new_temperature;

      echo "Updated temperature: {$current_temperature}\n";
      \Eventsd\Eventsd::trigger("Temperature", $current_temperature);
    }
  }
});
