<?php
if(!isset($_SESSION)) session_start();
!isset($_SERVER["HTTPS"]) || strtolower($_SERVER["HTTPS"])!="on" ? define("PROTOCOL", "http://") : define("PROTOCOL", "https://");

define("SQLSERVER", 'localhost');
define("SQLUSER", 'root');
define("SQLPASS", '');
define("SQLDB", 'kovinskesapiceDB');

define("REW_ROOT", '/');

define("WWW_ROOT", PROTOCOL.'kovinskesapice'.REW_ROOT);

define("WEB_ROOT", 'C:/wamp64/www/kovinskesapice'.REW_ROOT);

define("DO_CACHE", false);
define("FILE", "index.php");
define("PRODUCTION", false);
define("INCLUDE_LANG", true);