<?php

// Database Settings
if(isset($_ENV['MYSQL_ENV_MYSQL_USER']) && isset($_ENV['MYSQL_ENV_MYSQL_PASS']) && isset($_ENV['MYSQL_PORT'])) {
  $mysql_port = str_replace("/", "\\", str_replace("tcp://","",$_ENV['MYSQL_PORT']));
  $mysql_port = explode(":", $mysql_port, 2);
  $database = new \Thru\ActiveRecord\DatabaseLayer(array(
    'db_type' => 'Mysql',
    'db_hostname' => $mysql_port[0],
    'db_port' => $mysql_port[1],
    'db_username' => $_ENV['MYSQL_ENV_MYSQL_USER'],
    'db_password' => $_ENV['MYSQL_ENV_MYSQL_PASS'],
    'db_database' => 'interventio',
  ));
}elseif(isset($_ENV['DATABASE_URL'])){
  $dsn = str_replace("/", "\\", str_replace("mysql2://","",$_ENV['DATABASE_URL']));
  $dsn_fragments = \Thru\ActiveRecord\DatabaseLayer::ParseDsn($dsn);
  $database = new \Thru\ActiveRecord\DatabaseLayer(array(
    'db_type'     => 'Mysql',
    'db_hostname' => $dsn_fragments['host'],
    'db_port'     => $dsn_fragments['port'],
    'db_username' => $dsn_fragments['user'],
    'db_password' => $dsn_fragments['password'],
    'db_database' => $dsn_fragments['database'],
  ));
}else{
  $database = new \Thru\ActiveRecord\DatabaseLayer(array(
    'db_type'     => 'Mysql',
    'db_hostname' => 'milan.vpn.thru.io',
    'db_port'     => '3306',
    'db_username' => 'homeagent',
    'db_password' => 'honsd904ndfpippwe',
    'db_database' => 'homeagent'
  ));
}