<?php 
$actual_url = $_SERVER['REQUEST_URI'];
if(!isset($_SERVER['HTTP_REFERER'])) $_SERVER['HTTP_REFERER'] = $actual_url;

require(dirname(__FILE__).'/inc/config.php');
require(WEB_ROOT.'inc/site_functions.php');
require(WEB_ROOT.'libs/rewrite.php');
require(WEB_ROOT.'libs/vendor/autoload.php');
$templates = new League\Plates\Engine('libs/templates');

$instance = Connection::link();
$conn = $instance->getConnection();

if (isset($par1) && !isset($par2)) {
    $sql = "SELECT * FROM pages WHERE pretty_url = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $par1);
    $stmt->execute();
    $results = $stmt->get_result();
    if($row = $results->fetch_assoc()) {                
            $stranica = $row['pretty_url'];
            $result['template'] = $row['template'];
    }
    else {        
            header("HTTP/1.1 404 Not Found");include_once(WEB_ROOT.'/404/404.php');die();
    }      
}
else {
    $result['template']='naslovnica';
    $stranica = '';
} 



$templates->addData(['lang' => "hr", 'bodyClass' => 'home', 'pageTitle' => 'Kovinske Šapice'], 'home');

echo $templates->render($result['template'], ['currentPage' => $stranica]);
?>