<?php

$loop->addPeriodicTimer(1, function() {
  echo "♥\n";
  \Eventsd\Eventsd::heartbeat();
});

