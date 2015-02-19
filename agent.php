<?php
require_once("vendor/autoload.php");
$i = 0;

$loop = React\EventLoop\Factory::create();

require_once("agents/netscan.php");

$loop->run();
