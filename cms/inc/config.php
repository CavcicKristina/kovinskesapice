<?php
if(!isset($_SESSION)) session_start();

# Spajanje na bazu
define("SQLSERVER", 'localhost');
define("SQLUSER", 'root');
define("SQLPASS", '');
define("SQLDB", 'kovinskesapiceDB');

header('Content-Type: text/html; charset=utf-8');
header("Access-Control-Allow-Origin: *");

#REWRITE WEB
define_safe("REW_ROOT", '/');

# DOMENA
define("DOMAIN", "http://kovinskesapice".REW_ROOT);

# CMS ROOT WEB-a
define_safe("CMS_WWW_ROOT", DOMAIN.'cms'.REW_ROOT);

# CMS ROOT filea
define_safe("CMS_WEB_ROOT", 'C:/wamp64/www/kovinskesapice/cms'.REW_ROOT);

# Trebam za upload slika unutar CMS-a
define("WEB_ROOT", 'C:/wamp64/www/kovinskesapice'.REW_ROOT);

# ROOT WEb-a
define_safe("WWW_ROOT", DOMAIN);

?>