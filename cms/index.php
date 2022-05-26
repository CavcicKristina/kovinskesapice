<?php 

$actual_url = $_SERVER['REQUEST_URI'];
if(!isset($_SERVER['HTTP_REFERER'])) $_SERVER['HTTP_REFERER'] = $actual_url;

$path  = realpath(__DIR__);
include ($path.'/inc/functions.php');
include ($path.'/inc/config.php');

$conn = ConnectDB();

if(isset($_GET['logout']) && $_GET['logout'] == true){
    logOut();
    die(header("Location: ".CMS_WWW_ROOT."login.php"));
} elseif (empty($_SESSION)){
    die(header("Location: login.php"));
}

if (getUserLogged()) include ($path.'/cms_main.php');
?>