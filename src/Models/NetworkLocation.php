<?php
/**
 * Created by PhpStorm.
 * User: Matthew Baggett
 * Date: 19/02/2015
 * Time: 09:17
 */

namespace Intervent\HomeAgents\Models;

use Thru\ActiveRecord\ActiveRecord;

/**
 * Class NetworkLocation
 * @package Intervent\HomeAgents\Models
 * @var $network_location_id int
 * @var $ip text 15
 * @var $hostname text
 * @var $last_seen date
 */
class NetworkLocation extends ActiveRecord{
  protected $_table = "network_locations";

  public $network_location_id;
  public $ip;
  public $hostname = "";
  public $last_seen;
}