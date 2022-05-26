<?php

if ($_SERVER['SERVER_NAME'] == "localhost" || substr($_SERVER['SERVER_NAME'], 0, -3) == "192.168.1"){
	$_SERVER['REQUEST_URI'] = str_replace(REW_ROOT,'',$_SERVER['REQUEST_URI']);
}

##############################################
#   REWRITE OPCIJE I PRIPREMA                #
##############################################

$uri=$_SERVER['REQUEST_URI'];

function getParameterArray($uri){
	while (substr($uri, 0, 1) == '/'){ 
		$uri = substr($uri, 1);//$uri=> Ispisujemo sve osim prvog znaka #
	} 
	return explode('/', $uri); 
}

$pars = array();
$pars = getParameterArray($uri); 

$br=0;
for ($i = 0; $i < count($pars); $i++){
	${'par'.++$br} = urlencode($pars[$i]);
}


#############################################

?>