<?php
$path  = realpath(__DIR__);
include ($path.'/inc/functions.php');
include ($path.'/inc/config.php');

//header('Content-Type: text/html; charset=utf-8');
$conn = ConnectDB();

if(getUserLogged() == true){	
    die(header("Location: index.php"));
    //exit();
} elseif(($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['Submit']) && $_POST['Submit']=='signin'){
	if(Authenticate()) die(header("Location: index.php"));
	else die(header("Location: login.php"));
}
else {
	// log-> u bazu IP koji dolazi na login.php; nakon POST-a vrsta greške ili uspješno logiranje-> baza	
	//print_r('gle konju krivo si upisao lozinku');	
	//header("Location: login.php");	
}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Hydra CMS</title>
		 <meta name="robots" content="NONE,NOARCHIVE" />
		 <meta content="initial-scale=1, shrink-to-fit=no, width=device-width" name="viewport">        
        
        <!-- Load JQuery & Bootstrap 4 -->
        <script src="<?=CMS_WWW_ROOT?>js/jquery/jquery-3.5.1.min.js"></script>
        <link rel="stylesheet" href="<?=CMS_WWW_ROOT?>css/bootstrap.min.css">
        <script src="<?=CMS_WWW_ROOT?>js/bootstrap/popper.min.js"></script>
        <script src="<?=CMS_WWW_ROOT?>js/bootstrap/bootstrap.min.js"></script>

        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&family=Poppins&display=swap" rel="stylesheet"> 
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&family=Poppins:wght@300&display=swap" rel="stylesheet"> 
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&family=Poppins:wght@300;700&display=swap" rel="stylesheet"> 
        
        <!-- CMS CSS STYLE -->
        <link rel="stylesheet" href="<?=CMS_WWW_ROOT?>css/style.css">
				
		<script type="text/javascript">
			$(document).ready(function() {
				$("html").hide();
				$("html").fadeIn(900);
				function show_loading(){
					$("#loading_box").fadeOut(1500);
			   	};
			   	window.setTimeout( show_loading, 1500 );
			});
		</script>

</head>
<body>
     <main class="login">
	 <div class="container-fluid p-0">
		    <div class="row no-gutters ">
                <div class="col-10 col-md-4 mx-auto my-auto p-4 login-form">
                    <form action="<?=htmlentities($_SERVER['PHP_SELF'])?>" method="POST">
                        <div class="form-group">
                            <h1>CMS LOGIN</h1>
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" placeholder="Username...">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" placeholder="Password...">
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="Submit" value="signin">
                            <input type="submit" value="Login" class="login-submit">
                        </div>
                    </form>
                </div>
		    </div>
		 </div>
     </main>
     <footer></footer>
</body>
</html>