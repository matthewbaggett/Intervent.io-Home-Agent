<?php

$loop->addPeriodicTimer(30, function() {
  exec("gpspipe -w -n 10 | grep -m 1 lon > /tmp/gpslocation.json");
  usleep(5000);
  $json = file_get_contents("/tmp/gpslocation.json");
  unlink("/tmp/gpslocation.json");
  $location = json_decode($json);

  \Eventsd\Eventsd::trigger("GPSLocation", [
    'latitude' => $location->lat,
    'longitude' => $location->lon,
    'altitude' => $location->alt,
    'speed' => $location->speed
  ]);
});

