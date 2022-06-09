<?php
include('../inc/functions.php');
include('../inc/config.php');
$conn = ConnectDB();
if (!getUserLogged()){
   session_destroy();
   header("Location: ".CMS_WWW_ROOT."index.php");
}
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
define('DOING_AJAX', true);
    
if(IS_AJAX && DOING_AJAX) {

if(isset($_POST['field']) && isset($_POST['value']) && isset($_POST['id'])){
   $field = mysqli_real_escape_string($conn,trim($_POST['field']));
   $value = mysqli_real_escape_string($conn,trim($_POST['value']));
   $editid = mysqli_real_escape_string($conn,trim($_POST['id']));

   $query = "UPDATE `articles` SET ".$field."='".$value."' WHERE id=".$editid;
   $result = $conn->query($query);

   echo 1;
}else{
   echo 0;
}
exit;
}
exit;

