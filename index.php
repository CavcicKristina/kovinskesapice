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

$blog_id = '';
$blogs_page = '';
$lang = "en";

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
} elseif(isset($par1) && isset($par2)){ 
    if(strcmp($par1,'novosti') == 0){
        $stranica = 'novosti';
        $result['template'] = 'novosti';
        // Ako je broj stranice
        if(is_numeric($par2)){
                if(empty($par2)) $par2 = 1;
                $blogs_page = preg_replace('/[^0-9]/', '', $par2);
                $_SESSION['blogs_page'] = $blogs_page;
        } else {
                    if($selArticle=selectArticle($par2)){
                            $stranica = $selArticle['title'];
                            $result['template'] = 'blog_article';                                
                            $blog_id=$selArticle['id'];
                    }
            }
            
    }elseif(strcmp($par1,'udomi-psa') == 0 || strcmp($par1,'udomi-macku') == 0){
        $stranica = $par1;
        $result['template'] = 'animal_gallery';
        // Ako je broj stranice
        if(is_numeric($par2)){
                if(empty($par2)) $par2 = 1;
                $blogs_page = preg_replace('/[^0-9]/', '', $par2);
                $_SESSION['blogs_page'] = $blogs_page;
        } else {
                if($selArticle=selectAnimal($par1,$par2)){
                $stranica = $selArticle['animal_link'];
                $result['template'] = 'single_animal';                                
                $blog_id=$selArticle['id'];
                }
        }
            
    }
} else {
    $result['template']='naslovnica';
    $stranica = '';
}

if(empty($stranica)) $stranica = "KovinskeSapice";
$pageTitle = ucwords(str_replace('-', ' ', $stranica));


$templates->addData(['lang' => $lang, 'bodyClass' => 'home', 'pageTitle' => $pageTitle], 'home');

echo $templates->render($result['template'], [
    'currentPage' => $stranica,
    'blogs_page'=>$blogs_page,
    'blog_id'=>$blog_id,
    'parent' => $par1
]);
?>