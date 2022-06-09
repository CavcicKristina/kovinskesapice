<?php
include('config.php');
include('site_functions.php');
session_start();
header("Access-Control-Allow-Origin: *");
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
define('DOING_AJAX', true);
    
if(IS_AJAX && DOING_AJAX) {    
     if(isset($_POST['token']) && !empty($_POST['token']) && $_POST['token'] == $_SESSION['token']){
        $poruka="poruka-".$_SESSION['token'];             
        if(isset($_POST['email']) && !empty($_POST['email']) && isset($_POST[$poruka]) && !empty($_POST[$poruka])){     
            
            $instance = Connection::link();
            $conn = $instance->getConnection();
            $stmt = $conn->prepare("SELECT * FROM `contact` WHERE `type` = 5 OR `type` = 6");
            $stmt->execute();
            $results = $stmt->get_result();
            while($row = $results->fetch_assoc()){
                if($row['type'] == 5){
                    $to_mail = $row['content'];
                } elseif($row['type'] == 6){
                    $from_mail = $row['content'];
                }
            }
            
            $validateEmail = trim($_POST['email']);
            $validateEmail = stripslashes($validateEmail);
            $validateEmail = htmlspecialchars($validateEmail);
            $validateMessage = trim($_POST[$poruka]);
            $validateMessage = stripslashes($validateMessage);
            $validateMessage = htmlspecialchars($validateMessage);
            $to = $to_mail;
            $subject = "KovinskeSapice - upit sa stranice";
            $txt = "Kontakt sa forme: ".$validateEmail."\r\n"."Poruka: ".$validateMessage;
            $headers = "From: KovinskeSapice Pošta ".$from_mail."\r\n";
            if(mail($to,$subject,$txt,$headers)) echo "ok";
            else return false;
        }
    } else return false;    
}
else return false;
?>