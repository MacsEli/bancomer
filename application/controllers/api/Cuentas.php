<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require 'Api.php';

class Cuenta extends Api {

  public $table_name = "Cuenta";

  public function __construct() {
      parent::__construct();
  }

   
}