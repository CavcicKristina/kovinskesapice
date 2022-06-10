<?php
include('config.php');
include('site_functions.php');
/* session_start(); */
header("Access-Control-Allow-Origin: *");
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
define('DOING_AJAX', true);
    
if(IS_AJAX && DOING_AJAX) {    
     if(isset($_POST['token']) && !empty($_POST['token']) && $_POST['token'] == $_SESSION['token']){
        $poruka="poruka-".$_SESSION['token'];             
        if(isset($_POST['email']) && !empty($_POST['email']) && isset($_POST[$poruka]) && !empty($_POST[$poruka])){     
            
            $instance = Connection::link();
            $conn = $instance->getConnection();

            $validateEmail = trim($_POST['email']);
            $validateEmail = stripslashes($validateEmail);
            $validateEmail = htmlspecialchars($validateEmail);
            $validateMessage = trim($_POST[$poruka]);
            $validateMessage = stripslashes($validateMessage);
            $validateMessage = htmlspecialchars($validateMessage);

            $stmt = $conn->prepare("INSERT INTO `contact_msgs` (`name`, `content`, `date_created`) VALUES (?,?,NOW())");
            $stmt->bind_param("ss", $validateEmail, $validateMessage);
            if($stmt->execute()) echo "ok";
            else return false;
        }
    } else return false;    
}
else return false;
?>