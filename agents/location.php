<?php

$loop->addPeriodicTimer(1, function() {
  exec("gpspipe -w -n 10 | grep -m 1 lon", $output);
  var_dump($output);exit;
});

