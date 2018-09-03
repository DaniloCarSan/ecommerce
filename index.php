<?php 
session_name('lojavirtual');

session_start();

define('BASE_URL','http://www.hcodecommerce.com.br'.DIRECTORY_SEPARATOR);

define('ROOT',$_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR);
require_once("vendor/hcodebr/functions.php");

require_once("vendor/autoload.php");

use \Slim\Slim;

$app = new Slim();


$app->config('debug', true);

require_once("routes/site.php");

require_once("routes/admin-login.php");

require_once("routes/admin-forgot.php");

require_once("routes/admin-users.php");

require_once("routes/admin-categories.php");

require_once("routes/admin-products.php");

$app->run();

 ?>


