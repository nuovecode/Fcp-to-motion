<?php
/*
Item URI : http://www.ilgattohanuovecode.it
Author URI : Irene Iaccio (@nuovecode)
Version : 0.1
*/

error_reporting(E_ALL);

$site_path = realpath(dirname(__FILE__));
define ('__SITE_PATH', $site_path);

include_once("Controller/controller.php");

$controller = new Controller();
$controller->invoke();
