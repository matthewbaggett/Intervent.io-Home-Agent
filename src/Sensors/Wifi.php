<?php

/*
PHP class to wifi information on a unix system (I was bored, so I created this)
Copyright (C) 2007  Vegard Hammerseth <vegard@hammerseth.com> (http://vegard.hammerseth.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

v1.0.0
*/
namespace Intervent\HomeAgents\Sensors;

class Wifi
{
  public static $file         = "/proc/net/wireless";
  public static $iwconfig = "/sbin/iwconfig";
  public static $iwlist    = "/sbin/iwlist";

  public function devices()
  {
    $data = file_get_contents(wifi::$file);
    $data = explode("\n",$data);
    $data = array_slice($data,2);

    $devices = array();
    foreach ($data as $line)
    {
      if (empty($line))
      {
        continue;
      }

      $device        = explode(":",$line);
      $devices[]    = trim($device['0']);
    }
    return $devices;
  }


  public function device($arg_device)
  {
    $exec = shell_exec(escapeshellcmd(wifi::$iwconfig)." ".escapeshellarg($arg_device));

    /* iwconfig parser */
    $exec = str_replace(array("\n","\r"),array("  ","  "),$exec);
    $exec = implode("  ",preg_split("/\s\s+/",$exec));
    $exec = str_replace("  ","\n",$exec);

    $exec = explode("\n",$exec);
    $exec = array_merge(array($exec['0'].":".$exec['1']),array_slice($exec,2,-1));
    $return = array();
    var_dump($exec);
    foreach ($exec as $value_in)
    {
      if(count(explode(":",$value_in)) > 1){
        list($key,$value) = explode(":",$value_in);
      } else {
        list($key,$value) = explode("=",$value_in);
      }
      $value = str_replace("\"","",$value);
      $return[strtolower($key)] = $value;
    }

    return $return;
  }

  public function networks($arg_device)
  {
    $exec = shell_exec(escapeshellcmd(wifi::$iwlist)." ".escapeshellarg($arg_device)." scan");

    /* iwlist parser */
    $data = explode("\n",$exec);
    $data = array_slice($data,1,-2);

    $networks = array();
    $x = 0;
    foreach ($data as $line)
    {
      $line2 = explode(":",$line);
      $key      = strtolower(trim($line2['0']));
      $value = trim(implode(":",array_slice($line2,1)));

      if (substr($key,-7) == "address")
      {
        $key = "address";
      }
      elseif ($key == "essid")
      {
        $value = substr($value,1,-1);
      }
      elseif ($key == "frequency")
      {
        $value = explode(" ",$value);
        $networks[$x]['channel'] = substr($value['3'],0,-1);
        $value = $value['0']." ".$value['1'];
      }
      elseif ($key == "signal level")
      {
        $value = explode(":",$value);
        $networks[$x]['noise level'] = $value['1'];
        $value = implode(" ",array_slice(explode(" ",$value['0']),0,2));
      }
      elseif ($key == "encryption key")
      {
        $key = "key";
      }
      $networks[$x][$key] = $value;

      if ($key == "key")
      {
        $x++;
      }
    }

    return $networks;
  }
}
