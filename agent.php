<?php
require_once("vendor/autoload.php");
require_once("config/env.php");
require_once("config/database.php");
require_once("config/eventsd.php");
$i = 0;

$loop = React\EventLoop\Factory::create();

require_once("agents/netscan.php");

$loop->run();
