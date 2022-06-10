<?php

function ConnectDB(){
	static $Connected = false;
	
	if ($Connected) return;
	$Connected= true;

    $db = new mysqli(SQLSERVER, SQLUSER, SQLPASS, SQLDB);
    $db -> set_charset("utf8mb4");
    $db -> query("SET collation_connection = utf8mb4_general_ci");
    return $db;
}

function getCmsSettings(){
	global $conn;
	$sql = "SELECT
			`default_lang` AS H_LANG,
			`default_charset` AS H_CHARSET
			FROM `cms_settings` WHERE `id`='1' LIMIT 1";
	$result = $conn->query($sql);
	if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        foreach($row as $key => $val) {
                if (is_int($key)) continue;
                define_safe($key,$val);
            }
	}
	return true;
}

function getActiveLangs() {
    global $conn;

    $sql = "SELECT * FROM `lang`";
    $result = $conn->query($sql);
    $langs=[];
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()) {
        $langs[]= $row;
        }
        return $langs;
    }
    return false;
}

function getUserLogged(){
    global $conn;
	$auth_key = session_id();
	$auth_secret = sha1($_SERVER["SERVER_NAME"]);

    if (isset($_SESSION['user']) && isset($_SESSION['userid']) && isset($_SESSION['userkey'])){
		$usrname = strtolower(sessDecode($_SESSION['user']));
		$user_id = sessDecode($_SESSION['userid']);
		$usrkey = $_SESSION['userkey'];

		list($userkey, $crc, $user_id)= explode(',',$usrkey);
		if (md5($userkey.$auth_key)==$crc){
			$sql = "
                SELECT `id`, `username`
                FROM `cms_users` 
                WHERE username = '".mysqli_real_escape_string($conn,$usrname)." ' 
                AND id = '".mysqli_real_escape_string($conn,$user_id)."' 
				LIMIT 1";
			$res = $conn->query($sql);

			if(mysqli_num_rows($res)>0){
				$update = "
                    UPDATE `cms_users` SET `log_date`=NOW() WHERE `id`='".mysqli_real_escape_string($conn,$user_id)."'";
                $result = $conn->query($update);
				return true;
			} else return false;
    	} else return false;        
  	} else return false;
}

function Authenticate(){
    global $conn;
    $auth_key = session_id();

    /* autentikacija */
    /* if (count($_POST)==0){
		LogOut();
		return false;
	} */
    
    if(isset($_POST['username'])) $user = strip_tags(makeSafely($_POST['username']));
	if(isset($_POST['password'])) $pass = strip_tags(makeSafely($_POST['password']));

    $sql = "
            SELECT `id`, `username`, `log_date`, `password`
            FROM `cms_users`
            WHERE username = '".mysqli_real_escape_string($conn,$user)."'
		    LIMIT 1";
    if(!($result = $conn->query($sql))) return false;

    $result = mysqli_fetch_assoc($result);
    if(!password_verify($pass, $result['password'])) return false;
    
    /* zašita login sessiona */
    $userkey= session_id();
	$crc= md5($userkey.$auth_key);
	$key= $userkey.','.$crc.','.$result['id'];

    $_SESSION['user'] = sessEncode($result['username']);
    $_SESSION['log_date'] = $result['log_date'];
    $_SESSION['userid'] = sessEncode($result['id']);
	$_SESSION['userkey'] = $key;

    $sql_update = "
                    UPDATE `cms_users` 
                    SET `log_date`=NOW(),
                    `user_ip` = '".getIP()."'
                    WHERE `id` = '".$result['id']."'
                    ";
    $conn->query($sql_update);

    return true;
}

function logOut(){
	if(isset($_SESSION['userkey']) || isset($_SESSION['userid'])){
		$_SESSION['userkey'] = '';
		$_SESSION['userid'] = '';
		session_unset();
		session_destroy();
	}
}

/* provjerava varijable i njihove type-ove */
function makeSafely($check_var){
    if(is_array($check_var)){
		$check_var = filter_var_array($check_var, array(
		    "text" => FILTER_SANITIZE_ENCODED,
		    "id" => FILTER_VALIDATE_INT,
		    "title" => FILTER_SANITIZE_ENCODED,
		));
	} elseif(is_string($check_var)){
		$check_var = filter_var($check_var, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
	} elseif(is_int($check_var)){
		$check_var = filter_var($check_var, FILTER_VALIDATE_INT);
	} elseif(is_bool($check_var)){
		$check_var = filter_var($check_var, FILTER_VALIDATE_BOOLEAN);
	} elseif(is_float($check_var)){
		$check_var = filter_var($check_var, FILTER_VALIDATE_FLOAT);
	}
    return $check_var;
}

/* 
za kodiranje sessiona
$txtData - varijabla za zaštitu
$Level - nivo do koliko da se kodira
*/
function sessEncode($txtData,$Level='5'){
    for ($j = 0;$j<$Level;$j++){
        $tmpStr = '';
        for ($i = 0;$i<strlen($txtData);$i++)
            $tmpStr .= ord(substr(strtoupper($txtData), $i, 1));
        $txtData = $tmpStr;
    }
    return (strlen($Level)).$Level.$txtData;
}

/* 
Za dekodiranje sessiona
$txtData - varijabla za dekodiranje
*/
function sessDecode($txtData){
    $intLevel = substr($txtData, 1, substr($txtData, 0, 1));
    $startStr = substr($txtData, substr($txtData, 0, 1)+1, strlen($txtData));
    $tmpStr = '';
    for ($j = 0;$j<$intLevel;$j++){
        for ($i = 0;$i<strlen($startStr);$i+=2)
            $tmpStr .= chr(intval(substr($startStr, $i, 2)));
        $startStr = $tmpStr;

        $tmpStr = "";
    }
    return $startStr;
}

function getIP(){
	$ipaddress = '';
	if(isset($_SERVER['HTTP_CF_CONNECTING_IP'])){
	    $ipaddress =  $_SERVER['HTTP_CF_CONNECTING_IP'];
	} else if (isset($_SERVER['HTTP_X_REAL_IP'])){
	    $ipaddress = $_SERVER['HTTP_X_REAL_IP'];
	} else if (isset($_SERVER['HTTP_CLIENT_IP']))
	    $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	    $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	else if(isset($_SERVER['HTTP_X_FORWARDED']))
	    $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
	    $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	else if(isset($_SERVER['HTTP_FORWARDED']))
	    $ipaddress = $_SERVER['HTTP_FORWARDED'];
	else if(isset($_SERVER['REMOTE_ADDR']))
	    $ipaddress = $_SERVER['REMOTE_ADDR'];
	else
	    $ipaddress = 'UNKNOWN';

	return $ipaddress;

}

function unicode_urldecode($url){
    preg_match_all('/%u([[:alnum:]]{4})/', $url, $a);
 
    foreach ($a[1] as $uniord){
        $utf = '&#x' . $uniord . ';';
        $url = str_replace('%u'.$uniord, $utf, $url);
    }
 
    return urldecode($url);
}

function parseForSEO($name){

	$name = unicode_urldecode(trim($name));
	$ZABRANJENI_KARAKTERI = array("_", "|", "@", "§", "~", "?", "!", "#", '"', "%", "&", "=", "*", "'", "°", ".", ":", ";");
	$value = trim($name);
	$name = str_replace($ZABRANJENI_KARAKTERI, "", $value);

	//naši karakteri
	$name = str_replace("Ć","C",$name);
	$name = str_replace("Č","C",$name);
	$name = str_replace("Š","S",$name);
	$name = str_replace("Đ","DJ",$name);
	$name = str_replace("Ž","Z",$name);
	$name = str_replace("ć","c",$name);
	$name = str_replace("č","c",$name);
	$name = str_replace("š","s",$name);
	$name = str_replace("đ","dj",$name);
	$name = str_replace("ž","z",$name);

	$name = strtolower($name);
	$chrs = "1234567890-ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
	$len = strlen($name);
	$new_name = "";

	for($i=0; $i<$len; $i++){
		$c = substr($name, $i, 1);
		if (!is_int(strpos($chrs, $c))) $c = "-";
		$new_name .= $c;
	}
	//$new_name = str_replace('--','-',$new_name);
	//replace multiple occurences of - symbol
	$new_name = preg_replace('/--+/', '-', $new_name);

	//remove - from the beginning and the end:
	$new_name = trim($new_name, '-');
	
	//make default name
	if ($new_name == "") $new_name = "--";

	return strtolower($new_name);
}

## funkcija za kreiranje konstanti sa provjerom
# $name - ime
# $val - vrijednost
function define_safe($name, $val){
	if(!defined($name)) define("$name", $val);
}

## Funkcija za kreiranje prilagođenih slika
function resize_image($source_path, $target_path, $widht, $height, $method, $bg=-1){

    $image = $image = new Zebra_Image();
    
    $image->auto_handle_exif_orientation = false;

    /* originalna slika */
    $image->source_path = $source_path;

    /* ono što želim da slika bude */
    $image->target_path = $target_path;

    $image->preserve_aspect_ratio = true;
    $image->enlarge_smaller_images = true;
    $image->preserve_time = true;
    $image->handle_exif_orientation_tag = true;

    //if(empty($method)) $method = ZEBRA_IMAGE_CROP_CENTER;

    #if (!$image->resize($widht, $height, ZEBRA_IMAGE_CROP_CENTER)) {
    if (!$image->resize($widht, $height, $method, $bg)) {

        // if there was an error, let's see what the error is about
        switch ($image->error) {

            case 1:
                echo 'Source file could not be found!';
                break;
            case 2:
                echo 'Source file is not readable!';
                break;
            case 3:
                echo 'Could not write target file!';
                break;
            case 4:
                echo 'Unsupported source file format!';
                break;
            case 5:
                echo 'Unsupported target file format!';
                break;
            case 6:
                echo 'GD library version does not support target file format!';
                break;
            case 7:
                echo 'GD library is not installed!';
                break;
            case 8:
                echo '"chmod" command is disabled via configuration!';
                break;
            case 9:
                echo '"exif_read_data" function is not available';
                break;

        }

    } else return true;
}


function deleteArticle($id){
    global $conn;

    $delete_imgs = [];
    $id = (int)mysqli_real_escape_string($conn, trim($id));
    $result = $conn->query("SELECT content FROM article_content WHERE article_id = $id AND (content_type = 1 OR content_type = 4)");
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $delete_imgs[] = $row['content'];
        }
    }

    foreach($delete_imgs as $img){
        $parts = explode('.', $img);
        if(file_exists(WEB_ROOT.'images/content/article_uploads/'.$id.'-main-'.$img)) unlink(WEB_ROOT.'images/content/article_uploads/'.$id.'-main-'.$img);
    }

    //brisanje slika ubačenih u editor u folder articles-upload
    array_map('unlink', glob(WEB_ROOT.'images/content/article_uploads/'.$id.'-upload_*'));

    $id = mysqli_real_escape_string($conn, $id);
    $result = $conn->query("SELECT * FROM article_content WHERE article_id = $id");
    $article_content_num = $result->num_rows;
    for($i = 0; $i < $article_content_num; $i++){
        $stmt = $conn->query("DELETE FROM article_content WHERE article_id = $id");
    }

    $stmt = $conn->query("UPDATE articles SET deleted = 1 WHERE id = $id");

}

function getUsers($user_id=''){
    global $conn;
    $user_id = $conn->real_escape_string($user_id);
    $where='';
    $sql = "SELECT * from cms_users";
    if (!empty($user_id)) {$where = "WHERE id = '".$user_id."' LIMIT 1";}
    $sql = "SELECT * from cms_users $where";
    $result = $conn->query($sql);
    $all_users = [];
    if($result){
        while($row = $result->fetch_assoc()){
            foreach($row as $key => $value) {            
                if (!empty($user_id)) $all_users[$key] = $value;
                else $all_users[$row['id']][$key] = $value;
            }
        }
        return $all_users;
    }
    return false;
}

function updateCMSUser($conn, $username, $user_type, $id, $password, $password2){
    if ($user_type == 'delete') {
        $id=mysqli_real_escape_string($conn,trim($id));                            
        $sql_user_delete = "DELETE FROM `cms_users` WHERE `id` = $id";
        if ($conn->query($sql_user_delete)) return true; 
        return false;
    }

    /* provjera da li postoji isti username */
    $username = $conn->real_escape_string($username);
    $sql = "SELECT `username` FROM `cms_users` WHERE `username` = '".$username."'";
    $result = $conn->query($sql);
    if($result->num_rows > 1) return false;

    if(strcmp($password, $password2) == 0){
        $password_hash='';
        !empty($password) ? $password_hash = password_hash($password, PASSWORD_BCRYPT) : '';
        $ip =  getIP();

        if (!empty($password_hash)) {
            $stmt = $conn->prepare("UPDATE `cms_users` SET `username` = ?, `password` = ?, `membership` = ?, `user_ip` = ?, `log_date` = NOW() WHERE `id` = ? LIMIT 1");
            $stmt->bind_param("ssisi", $username, $password_hash, $user_type, $ip, $id);
        }
        else {
            $stmt = $conn->prepare("UPDATE `cms_users` SET `username` = ?, `membership` = ?, `user_ip` = ?, `log_date` = NOW() WHERE `id` = ? LIMIT 1");
            $stmt->bind_param("sisi", $username, $user_type, $ip, $id);
        }
                    
        if(!$stmt->execute()) return false;

    } else return false;    
}

function createCMSUser($conn, $username, $user_type, $password, $password2){
    $username = strtolower($username);
    /* dohvacaju se svi usernameovi */
    $all_users = [];
    $sql = "SELECT username FROM cms_users";
    $result = $conn->query($sql);
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $all_users[] = $row['username'];
        }
    } 
    var_dump($username,$user_type,$password,$password2);
    /* provjerava se ako ima isti username, ako ima ide error */
    foreach($all_users as $user){
        if(strcmp($user, $username) == 0){
            return false;
        }
    }
    
    /* provjera da li su sifre iste, ako ne, error, ako da, unosi korisnika u cms */
    if(strcmp($password, $password2) == 0){

        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO `cms_users` (`username`, `membership`, `password`, `user_ip`, `log_date`) VALUES (?,?,?,?,NOW())");

        $ip = getIP();
        $stmt->bind_param("siss", $username, $user_type, $password_hash, $ip);
        if(!$stmt->execute()) return false;
    } else {
        return false;
    }
    
}

function selectArticleType($category_id){
    global $conn;
    $data = [];
    $result = $conn->query("SELECT category_type FROM article_categories where id = ".$conn->real_escape_string($category_id));
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $data = $row['category_type'];
        }
        return $data;
    } else return false;
}

function selectAllArticles(){
    global $conn;

    $sql = "SELECT * FROM `articles` WHERE `deleted` = 0 ORDER BY `id` DESC";
	$result = $conn->query($sql);

    $i=0;
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){	
            $content[$i]['id'] = $row['id'];
            $content[$i]['title'] = $row['title'];
            $content[$i]['author'] = $row['author'];
            $content[$i]['active'] = $row['active'];
            $content[$i]['article_link'] = $row['article_link'];
            $content[$i]['view_count'] = $row['view_count'];
            $content[$i]['date_created'] = date("m-d-Y", strtotime($row['date_created']));
            $i++;
        }
    } else return false;

    $stmt = $conn->prepare("SELECT * FROM `article_imgs` WHERE `article_id` = ? AND `front` = 1 LIMIT 1");
    for($i = 0; $i < count($content); $i++){
        $stmt->bind_param("i", $content[$i]['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->free_result();
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()) $content[$i]['show'] = WWW_ROOT."images/articles/".$row['img'];
        }
    }

    return $content;
}

function selectAllThrownCategoryArticle(){
    global $conn;

    $sql = "SELECT * FROM `articles` WHERE `category_id` NOT IN (SELECT `id` FROM `article_categories`) AND `deleted` = 0 ORDER BY `id` DESC";
	$result = $conn->query($sql);
    
    if($result){
        $i=0;
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){	
                $content[$i]['id'] = $row['id'];
                $content[$i]['title'] = $row['title'];
                $content[$i]['author'] = $row['author'];
                if('0' === $row['front_img']) $content[$i]['show'] = html_entity_decode($row['header'], ENT_QUOTES, 'UTF-8');
                else $content[$i]['show'] = WWW_ROOT."images/content/article_uploads/".$row['front_img'];
                $content[$i]['active'] = $row['active'];
                $content[$i]['article_link'] = $row['article_link'];
                $content[$i]['category_id'] = $row['category_id'];
                $content[$i]['date_created'] = date("m-d-Y", strtotime($row['date_created']));
                $content[$i]['show_frontend'] = ('0' === $row['front_img']) ? 0 : 1;
                $i++;
            }
            return $content;
        }
    }
    return false;
}

function uploadNewArticle($content, $author, $slider_img){
    global $conn;

    $answer = $content['answer'];
    $lang = 1;

    $title=htmlentities($content['article_title'],ENT_QUOTES,"UTF-8");
    $paragraf = htmlentities($content['paragraf-1'],ENT_QUOTES,"UTF-8");
    $header=htmlentities($content['header'],ENT_QUOTES,"UTF-8");

    if($slider_img['name'][0] != '') $front_img = $slider_img['name'][0];
    else $front_img = '0';

    $title_url = parseForSEO($title).'%';
    $url = parseForSEO($title);
    $stmt = $conn->prepare("SELECT * FROM `articles` WHERE `article_link` LIKE ? AND `deleted` = 0 ORDER BY `article_link` DESC LIMIT 1");
    $stmt->bind_param("s", $title_url);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    $row = $result->fetch_assoc();
    if(!empty($row)) $url = parseForSEO($title.'1');

    $title=htmlentities($title, ENT_QUOTES, "UTF-8");
    $stmt = $conn->prepare("INSERT INTO articles (lang, title, header, author, content, article_link, active, date_created, deleted) VALUES (?,?,?,?,?,?,0,NOW(),0)");
    if(!$stmt->bind_param("isssss", $lang, $title, $header, $author, $paragraf, $url)) $stmt->error;
    $stmt->execute();
    $article_id = $conn->insert_id;

    if($slider_img['name'][0] != ''){
        for($i = 0; $i < count($slider_img['name']); $i++){

            $original_image_path = WEB_ROOT."images/articles/original/".$article_id."-".$slider_img['name'][$i];
            $file = $slider_img['name'][$i];
            $tmp_file = $slider_img['tmp_name'][$i];
            $img_name = pathinfo($file, PATHINFO_FILENAME);
            move_uploaded_file($tmp_file, $original_image_path);
            resize_image($original_image_path, $original_image_path, 825, 500, ZEBRA_IMAGE_BOXED);

            if($i == $answer){ 
                $front = 1;
                $frontImage = $article_id.'-'.$img_name.'.webp';
                $webp_image_path = WEB_ROOT."images/articles/".$frontImage;
                resize_image($original_image_path, $webp_image_path, 600, 400, ZEBRA_IMAGE_BOXED);
                $sql_img = $frontImage;
            } else {
                $front = 0;
                $sql_img = $article_id."-".$slider_img['name'][$i];
            }

            // baza            
            $stmt = $conn->prepare("INSERT INTO `article_imgs` (article_id, img, front) VALUES (?,?,?)");
            $stmt->bind_param("isi", $article_id, $sql_img, $front);
            $stmt->execute();

        }
        
    }

}

function deleteArticleWithCategory($id){
    global $conn;

    /* 
    dohvati sve slike, i prvo sve njih obriši (ALI PRVO OBRIŠI FRONT SLIKU)
    updateaj article da je delete 1
    */

    $delete_imgs = [];
    $id = (int)mysqli_real_escape_string($conn, trim($id));
    $stmt = $conn->prepare("SELECT * FROM `article_imgs` WHERE `article_id` = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $delete_imgs[] = $row;
        }
    }

    /* brisanje slika u folderu images/articles */
    foreach($delete_imgs as $img){
        if($img['front'] == 1){
            $change_front_name = str_replace('.webp','',$img['img']);
            /* traženje ekstenzije */
            if(file_exists(WEB_ROOT.'images/articles/original/'.$change_front_name.'.png')) $full_change_front_name = $change_front_name.'.png';
            if(file_exists(WEB_ROOT.'images/articles/original/'.$change_front_name.'.jpg')) $full_change_front_name = $change_front_name.'.jpg';
            if(file_exists(WEB_ROOT.'images/articles/original/'.$change_front_name.'.jpeg')) $full_change_front_name = $change_front_name.'.jpeg';
            if(file_exists(WEB_ROOT.'images/articles/original/'.$change_front_name.'.gif')) $full_change_front_name = $change_front_name.'.gif';
            if(file_exists(WEB_ROOT.'images/articles/original/'.$change_front_name.'.webp')) $full_change_front_name = $change_front_name.'.webp';

            if(file_exists(WEB_ROOT.'images/articles/original/'.$full_change_front_name)) unlink(WEB_ROOT.'images/articles/original/'.$full_change_front_name);
            if(file_exists(WEB_ROOT.'images/articles/'.$img['img'])) unlink(WEB_ROOT.'images/articles/'.$img['img']);

        } else {
            if(file_exists(WEB_ROOT.'images/articles/original/'.$img['img'])) unlink(WEB_ROOT.'images/articles/original/'.$img['img']);
        }
    }

    /* brisanje slika iz baze */
    $stmt = $conn->prepare("DELETE FROM `article_imgs` WHERE `article_id` = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    $stmt = $conn->prepare("UPDATE `articles` SET `deleted` = 1 WHERE `id` = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
}

function selectArticle($article_id){
    global $conn;

    $data = [];
    $last_position = 1;

    $stmt = $conn->prepare("SELECT * FROM `articles` WHERE `id` = ? AND `deleted` = 0");
    $stmt->bind_param("i",$article_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()) $data = $row;
    } else return false;

    $stmt = $conn->prepare("SELECT * FROM `article_imgs` WHERE `article_id` = ?");
    $stmt->bind_param("i",$article_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    $i = 0;
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){ 
            if($row['front'] == 1) $data['imgs'][$i] = WWW_ROOT."images/articles/".$row['img'];
            else $data['imgs'][$i] = WWW_ROOT."images/articles/original/".$row['img'];
            $i++;
        }
    } else $data['imgs'] = '';

    return $data;
}

function updateArticleWithCategory($content, $cms_user, $front_image){
    global $conn;
    $title = $content['article_title'];
    $header = $content['header'];
    $paragraf = $content['paragraf-1'];
    $article_id = $content['id'];
    
    $articleData = [];
    $stmt = $conn->prepare("SELECT * FROM `articles` WHERE `id` = ? ");
    $stmt->bind_param("i", $article_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $articleData = $row;
        }
    }
    $stmt = $conn->prepare("SELECT * FROM `article_imgs` WHERE `article_id` = ? ORDER BY `id` ASC");
    $stmt->bind_param("i", $article_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    if($result->num_rows > 0){
        $x = 0;
        while($row = $result->fetch_assoc()){
            $articleData['imgs'][$x] = $row;
            $x++;
        }
    }
    

    /* uploadaju se nove slike */
    if($front_image['name'][0] !== ''){
        // Obriši staru sliku
        foreach($articleData['imgs'] as $delete_img){
            if(file_exists(WEB_ROOT.'images/articles/'.$delete_img['img'])) unlink(WEB_ROOT.'images/articles/'.$delete_img['img']);
            if(file_exists(WEB_ROOT.'images/articles/original/'.$delete_img['img'])) unlink(WEB_ROOT.'images/articles/original/'.$delete_img['img']);
        }

        $stmt = $conn->prepare("DELETE FROM `article_imgs` WHERE `article_id` = ?");
        $stmt->bind_param("i", $article_id);
        $stmt->execute();
        $stmt->free_result();

        if($content['answer'] == '') $answer = 0;
        else $answer = (int) $content['answer'];

        for($i = 0; $i < count($front_image['name']); $i++){

            if($answer == $i){
                $original_image_path = WEB_ROOT."images/articles/original/".$article_id.'-'.$front_image['name'][$i];            
                $file = $front_image['name'][$i];
                $tmp_file = $front_image['tmp_name'][$i];
                $img_name = pathinfo($file, PATHINFO_FILENAME);
                move_uploaded_file($tmp_file, $original_image_path);
        
                $frontImage = $article_id.'-'.$img_name.'.webp';
                $webp_image_path = WEB_ROOT."images/articles/".$frontImage;
        
                resize_image($original_image_path, $webp_image_path, 600, 400, ZEBRA_IMAGE_BOXED);
            
                $stmt = $conn->prepare("INSERT INTO `article_imgs` (article_id, img, front) VALUES (?,?,1)");
                $stmt->bind_param("is", $article_id, $frontImage);
                $stmt->execute();
            } else {
                $original_image_path = WEB_ROOT."images/articles/original/".$article_id.'-'.$front_image['name'][$i];            
                $file = $front_image['name'][$i];
                $tmp_file = $front_image['tmp_name'][$i];
                $img_name = pathinfo($file, PATHINFO_FILENAME);
                move_uploaded_file($tmp_file, $original_image_path);
                resize_image($original_image_path, $original_image_path, 825, 500, ZEBRA_IMAGE_BOXED);
                $uploadimg = $article_id.'-'.$front_image['name'][$i];

                $stmt = $conn->prepare("INSERT INTO `article_imgs` (article_id, img, front) VALUES (?,?,0)");
                $stmt->bind_param("is", $article_id, $uploadimg);
                $stmt->execute();
            }

            
        }
    } elseif($content['answer'] != ''){
        $answer = (int) $content['answer'];
        $y = 0;
        
        foreach($articleData['imgs'] as $deleteimages){
            if($deleteimages['front'] == 1){ 
                $change_front_name = str_replace('.webp','',$deleteimages['img']);
                /* traženje ekstenzije */
                if(file_exists(WEB_ROOT.'images/articles/original/'.$change_front_name.'.png')) $full_change_front_name = $change_front_name.'.png';
                if(file_exists(WEB_ROOT.'images/articles/original/'.$change_front_name.'.jpg')) $full_change_front_name = $change_front_name.'.jpg';
                if(file_exists(WEB_ROOT.'images/articles/original/'.$change_front_name.'.jpeg')) $full_change_front_name = $change_front_name.'.jpeg';
                if(file_exists(WEB_ROOT.'images/articles/original/'.$change_front_name.'.gif')) $full_change_front_name = $change_front_name.'.gif';
                if(file_exists(WEB_ROOT.'images/articles/original/'.$change_front_name.'.webp')) $full_change_front_name = $change_front_name.'.webp';
                /* updateanje baze da front nije više front */
                $stmt = $conn->prepare("UPDATE `article_imgs` SET `img` = ?, `front` = 0 WHERE `article_id` = ? AND `front` = 1");
                $stmt->bind_param("si", $full_change_front_name, $article_id);
                $stmt->execute();
                $stmt->free_result();

                /* brisanje stare front slike */
                if(file_exists(WEB_ROOT.'images/articles/'.$deleteimages['img'])){ 
                    unlink(WEB_ROOT.'images/articles/'.$deleteimages['img']);
                    break;
                }
            }
        }
        foreach($articleData['imgs'] as $images){
            if($y == $answer){
                $img_id = $images['id'];
                $original_image_path = WEB_ROOT."images/articles/original/".$images['img'];
                $img_name = pathinfo($images['img'], PATHINFO_FILENAME);
        
                $frontImage = $img_name.'.webp';
                $webp_image_path = WEB_ROOT."images/articles/".$frontImage;
        
                resize_image($original_image_path, $webp_image_path, 600, 400, ZEBRA_IMAGE_BOXED);
            
                $stmt = $conn->prepare("UPDATE `article_imgs` SET `front` = 1, `img` = ? WHERE `id` = ?");
                $stmt->bind_param("si", $frontImage, $img_id);
                $stmt->execute();
                
                break;
            } else{
                $y++;
            }
        }
    }

    $title_url = parseForSEO($title).'%';
    $url = parseForSEO($title);
    $stmt = $conn->prepare("SELECT * FROM `articles` WHERE `article_link` LIKE ? AND `deleted` = 0 ORDER BY `article_link` DESC LIMIT 1");
    $stmt->bind_param("s", $title_url);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    $row = $result->fetch_assoc();
    if(!empty($row) && $row['id']!=$article_id) $url = parseForSEO($title.'1');

    // Update title i header i paragraf
    $title=htmlentities($title,ENT_QUOTES,"UTF-8");
    $paragraf=htmlentities($paragraf,ENT_QUOTES,"UTF-8");
    $header=htmlentities($header,ENT_QUOTES,"UTF-8");
    $stmt = $conn->prepare("UPDATE `articles` SET `title` = ?, `header` = ?, `content` = ?, `article_link` = ?, `author` = ? WHERE `id` = ?");
    $stmt->bind_param("sssssi", $title, $header, $paragraf, $url, $user, $article_id);

    $stmt->execute();
}

function filter_filename($filename, $beautify=true) {
    // sanitize filename
    $filename = preg_replace(
        '~
        [<>:"/\\\|?*]|
        [\x00-\x1F]|
        [\x7F\xA0\xAD]|
        [#\[\]@!$&\'()+,;=]|
        [{}^\~`]
        ~x',
        '-', $filename);
    $filename = ltrim($filename, '.-');
    if ($beautify) $filename = beautify_filename($filename);
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $filename = mb_strcut(pathinfo($filename, PATHINFO_FILENAME), 0, 255 - ($ext ? strlen($ext) + 1 : 0), mb_detect_encoding($filename)) . ($ext ? '.' . $ext : '');
    return $filename;
}

function beautify_filename($filename) {
    // reduce consecutive characters
    $filename = preg_replace(array(
        '/ +/',
        '/_+/',
        '/-+/'
    ), '-', $filename);
    $filename = preg_replace(array(
        '/-*\.-*/',
        '/\.{2,}/'
    ), '.', $filename);
    $filename = mb_strtolower($filename, mb_detect_encoding($filename));
    $filename = trim($filename, '.-');
    return $filename;
}

function selectArticleLinks(){
    global $conn;
    $data=[];
    $query="SELECT a.title, a.article_link, b.category_name FROM `articles` as a
    LEFT JOIN `article_categories` as b ON a.category_id=b.id
    WHERE a.active = '1' AND a.deleted='0' ORDER BY a.title";
    $result = $conn->query($query);
    while($row =$result ->fetch_assoc()){
        $data[]=$row;
    }
    return $data;
}

// diverse array za $_FILES, presloženo u korisniji array
function diverse_array($vector) {
    $result = array();
    foreach($vector as $key1 => $value1)
        foreach($value1 as $key2 => $value2)
            $result[$key2][$key1] = $value2;
    return $result;
}

function filterArrayWithPrefix($array, $prefix){
    $keys = array_keys($array);
    $result = array();

    foreach ($keys as $key)
    {
        if (strpos($key, $prefix) === 0)
        {
            $result[$key] = $array[$key];
        }
    }
    return $result;
}

function formatDate($date){
	/* Uzima datum string, yyyy-mm-dd i napravi dd.mm.yyyy. */
	$unformat_date = explode('-', $date);
	$new_date[0] = $unformat_date[2];
	$new_date[1] = $unformat_date[1];
	$new_date[2] = $unformat_date[0];
	$date = implode('.', $new_date);
	$date .= '.';
	return $date;
}

function getPageViews($lang){
    global $conn;
    $data=[];
    $query="SELECT id, content, view_count FROM `meni` WHERE `link` > '' ORDER BY `order`";
    $result = $conn->query($query);
    while($row =$result ->fetch_assoc()){
        $data[$row['id']]=$row;
    }
    return $data;
}

function selectAllDogs(){
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM `dogs` WHERE `deleted` = 0");
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    $data = [];
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()) $data[] = $row;

        $stmt = $conn->prepare("SELECT * FROM `dog_imgs` WHERE `dog_id` = ? AND `front` = 1");
        $i = 0;
        foreach($data as $dog){
            $stmt->bind_param("i",$dog['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->free_result();
            if($result->num_rows > 0){
                $row = $result->fetch_assoc();
                $data[$i]['front_img'] = WWW_ROOT.'images/animals/'.$row['img'];
            } else {
                $data[$i]['front_img'] = '';
            }
            $i++;
        }

        return $data;
    } else return false;
}

function selectAllCats(){
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM `cats` WHERE `deleted` = 0");
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    $data = [];
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()) $data[] = $row;

        $stmt = $conn->prepare("SELECT * FROM `cat_imgs` WHERE `cat_id` = ? AND `front` = 1");
        $i = 0;
        foreach($data as $dog){
            $stmt->bind_param("i",$dog['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->free_result();
            if($result->num_rows > 0){
                $row = $result->fetch_assoc();
                $data[$i]['front_img'] = WWW_ROOT.'images/animals/'.$row['img'];
            } else {
                $data[$i]['front_img'] = '';
            }
            $i++;
        }

        return $data;
    } else return false;
}

function uploadNewDog($dog_info, $dog_photo){
    global $conn;

    if($dog_info['answer'] != '') $answer = $dog_info['answer'];
    else $answer = 0;
    if(isset($dog_info['spol'][0])) $spol = $dog_info['spol'][0];
    if(isset($dog_info['dob'][0])) $dob = $dog_info['dob'][0];
    if(isset($dog_info['cijepljen'])) $cijepljen = $dog_info['cijepljen'];
    else $cijepljen = 0;
    if(isset($dog_info['cipiran'])) $cipiran = $dog_info['cipiran'];
    else $cipiran = 0;
    if(isset($dog_info['kastriran'])) $kastriran = $dog_info['kastriran'];
    else $kastriran = 0;
    if(isset($dog_info['slaganje'])) $slaganje = $dog_info['slaganje'];
    else $slaganje = 0;
    if(isset($dog_info['socijaliziran'])) $socijaliziran = $dog_info['socijaliziran'];
    else $socijaliziran = 0;
    if(isset($dog_info['plah'])) $plah = $dog_info['plah'];
    else $plah = 0;
    if(isset($dog_info['aktivniji'])) $aktivniji = $dog_info['aktivniji'];
    else $aktivniji = 0;
    if(isset($dog_info['manje-aktivni'])) $manje_aktivni = $dog_info['manje-aktivni'];
    else $manje_aktivni = 0;
    if(isset($dog_info['paragraf-1'])) $opis = htmlentities($dog_info['paragraf-1'],ENT_QUOTES,"UTF-8");
    else $opis = '';
    $lang = 1;

    $dog_name=htmlentities($dog_info['article_title'],ENT_QUOTES,"UTF-8");
    $dog_breed=htmlentities($dog_info['pasmina'],ENT_QUOTES,"UTF-8");
    $dog_size=htmlentities($dog_info['velicina'],ENT_QUOTES,"UTF-8");

    $animal_url = parseForSEO($dog_name).'%';
    $url = parseForSEO($dog_name);
    $stmt = $conn->prepare("SELECT * FROM `dogs` WHERE `animal_link` LIKE ? AND `deleted` = 0 ORDER BY `animal_link` DESC LIMIT 1");
    $stmt->bind_param("s", $animal_url);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    $row = $result->fetch_assoc();
    if(!empty($row)) $url = parseForSEO($dog_name.'1');

    $stmt = $conn->prepare("INSERT INTO `dogs` (`name`, `spol`, `dob`, `cijepljen`, `cipiran`, `kastriran`, `slaganje`, `socijaliziran`, `plah`, `aktivniji`, `manje_aktivni`, `opis`, `animal_link`, `pasmina`, `velicina`, `active`, `deleted`, `view_count`, `date_created`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,0,0,0,NOW())");
    if(!$stmt->bind_param("sssiiiiiiiissss", $dog_name, $spol, $dob, $cijepljen, $cipiran, $kastriran, $slaganje, $socijaliziran, $plah, $aktivniji, $manje_aktivni, $opis, $url, $dog_breed, $dog_size)) $stmt->error;
    $stmt->execute();
    $dog_id = $conn->insert_id;

    if($dog_photo['name'][0] != ''){
        for($i = 0; $i < count($dog_photo['name']); $i++){

            $original_image_path = WEB_ROOT."images/animals/original/".$dog_id."-".$dog_photo['name'][$i];
            $file = $dog_photo['name'][$i];
            $tmp_file = $dog_photo['tmp_name'][$i];
            $img_name = pathinfo($file, PATHINFO_FILENAME);
            move_uploaded_file($tmp_file, $original_image_path);

            if($i == $answer){ 
                $front = 1;
                $frontImage = $dog_id.'-'.$img_name.'.webp';
                $webp_image_path = WEB_ROOT."images/animals/".$frontImage;
                resize_image($original_image_path, $webp_image_path, 830, 475, ZEBRA_IMAGE_BOXED);
                $sql_img = $frontImage;
            } else {
                $thumb_path = WEB_ROOT."images/animals/thumbs/".$dog_id."-".$dog_photo['name'][$i];
                resize_image($original_image_path, $thumb_path, 350, 260, ZEBRA_IMAGE_BOXED);
                $front = 0;
                $sql_img = $dog_id."-".$dog_photo['name'][$i];
            }

            // baza            
            $stmt = $conn->prepare("INSERT INTO `dog_imgs` (`dog_id`, `img`, `front`) VALUES (?,?,?)");
            $stmt->bind_param("isi", $dog_id, $sql_img, $front);
            $stmt->execute();
        }
        return true;
    }
}

function uploadNewCat($dog_info, $dog_photo){
    global $conn;

    if($dog_info['answer'] != '') $answer = $dog_info['answer'];
    else $answer = 0;
    if(isset($dog_info['spol'][0])) $spol = $dog_info['spol'][0];
    if(isset($dog_info['dob'][0])) $dob = $dog_info['dob'][0];
    if(isset($dog_info['cijepljen'])) $cijepljen = $dog_info['cijepljen'];
    else $cijepljen = 0;
    if(isset($dog_info['cipiran'])) $cipiran = $dog_info['cipiran'];
    else $cipiran = 0;
    if(isset($dog_info['kastriran'])) $kastriran = $dog_info['kastriran'];
    else $kastriran = 0;
    if(isset($dog_info['slaganje'])) $slaganje = $dog_info['slaganje'];
    else $slaganje = 0;
    if(isset($dog_info['socijaliziran'])) $socijaliziran = $dog_info['socijaliziran'];
    else $socijaliziran = 0;
    if(isset($dog_info['plah'])) $plah = $dog_info['plah'];
    else $plah = 0;
    if(isset($dog_info['aktivniji'])) $aktivniji = $dog_info['aktivniji'];
    else $aktivniji = 0;
    if(isset($dog_info['manje-aktivni'])) $manje_aktivni = $dog_info['manje-aktivni'];
    else $manje_aktivni = 0;
    if(isset($dog_info['paragraf-1'])) $opis = htmlentities($dog_info['paragraf-1'],ENT_QUOTES,"UTF-8");
    else $opis = '';
    $lang = 1;

    $dog_name=htmlentities($dog_info['article_title'],ENT_QUOTES,"UTF-8");
    $dog_breed=htmlentities($dog_info['pasmina'],ENT_QUOTES,"UTF-8");
    $dog_size=htmlentities($dog_info['velicina'],ENT_QUOTES,"UTF-8");

    $animal_url = parseForSEO($dog_name).'%';
    $url = parseForSEO($dog_name);
    $stmt = $conn->prepare("SELECT * FROM `cats` WHERE `animal_link` LIKE ? AND `deleted` = 0 ORDER BY `animal_link` DESC LIMIT 1");
    $stmt->bind_param("s", $animal_url);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    $row = $result->fetch_assoc();
    if(!empty($row)) $url = parseForSEO($dog_name.'1');

    $stmt = $conn->prepare("INSERT INTO `dogs` (`name`, `spol`, `dob`, `cijepljen`, `cipiran`, `kastriran`, `slaganje`, `socijaliziran`, `plah`, `aktivniji`, `manje_aktivni`, `opis`, `animal_link`, `pasmina`, `velicina`, `active`, `deleted`, `view_count`, `date_created`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,0,0,0,NOW())");
    if(!$stmt->bind_param("sssiiiiiiiissss", $dog_name, $spol, $dob, $cijepljen, $cipiran, $kastriran, $slaganje, $socijaliziran, $plah, $aktivniji, $manje_aktivni, $opis, $url, $dog_breed, $dog_size)) $stmt->error;
    $stmt->execute();
    $dog_id = $conn->insert_id;

    if($dog_photo['name'][0] != ''){
        for($i = 0; $i < count($dog_photo['name']); $i++){

            $original_image_path = WEB_ROOT."images/animals/original/".$dog_id."-".$dog_photo['name'][$i];
            $file = $dog_photo['name'][$i];
            $tmp_file = $dog_photo['tmp_name'][$i];
            $img_name = pathinfo($file, PATHINFO_FILENAME);
            move_uploaded_file($tmp_file, $original_image_path);
            resize_image($original_image_path, $original_image_path, 350, 260, ZEBRA_IMAGE_BOXED);

            

            if($i == $answer){ 
                $front = 1;
                $frontImage = $dog_id.'-'.$img_name.'.webp';
                $webp_image_path = WEB_ROOT."images/animals/".$frontImage;
                resize_image($original_image_path, $webp_image_path, 830, 475, ZEBRA_IMAGE_BOXED);
                $sql_img = $frontImage;
            } else {
                $thumb_path = WEB_ROOT."images/animals/thumbs/".$dog_id."-".$dog_photo['name'][$i];
                resize_image($original_image_path, $thumb_path, 350, 260, ZEBRA_IMAGE_BOXED);
                $front = 0;
                $sql_img = $dog_id."-".$dog_photo['name'][$i];
            }

            // baza            
            $stmt = $conn->prepare("INSERT INTO `cat_imgs` (`cat_id`, `img`, `front`) VALUES (?,?,?)");
            $stmt->bind_param("isi", $dog_id, $sql_img, $front);
            $stmt->execute();
            
        }
        return true;
    }
}

function selectDog($id){
    global $conn;

    $data = [];

    $stmt = $conn->prepare("SELECT * FROM `dogs` WHERE `id` = ? AND `deleted` = 0");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()) $data = $row;
    } else return false;

    $stmt = $conn->prepare("SELECT * FROM `dog_imgs` WHERE `dog_id` = ?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    $i = 0;
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){ 
            if($row['front'] == 1) $data['imgs'][$i] = WWW_ROOT."images/animals/".$row['img'];
            else $data['imgs'][$i] = WWW_ROOT."images/animals/thumbs/".$row['img'];
            $i++;
        }
    } else $data['imgs'] = '';

    return $data;
}

function selectCat($id){
    global $conn;

    $data = [];

    $stmt = $conn->prepare("SELECT * FROM `cats` WHERE `id` = ? AND `deleted` = 0");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()) $data = $row;
    } else return false;

    $stmt = $conn->prepare("SELECT * FROM `cat_imgs` WHERE `cat_id` = ?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    $i = 0;
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){ 
            if($row['front'] == 1) $data['imgs'][$i] = WWW_ROOT."images/animals/".$row['img'];
            else $data['imgs'][$i] = WWW_ROOT."images/animals/thumbs/".$row['img'];
            $i++;
        }
    } else $data['imgs'] = '';

    return $data;
}

function updateDog($dog_info, $dog_photo){
    global $conn;

    if(isset($dog_info['spol'][0])) $spol = $dog_info['spol'][0];
    if(isset($dog_info['dob'][0])) $dob = $dog_info['dob'][0];
    if(isset($dog_info['cijepljen'])) $cijepljen = $dog_info['cijepljen'];
    else $cijepljen = 0;
    if(isset($dog_info['cipiran'])) $cipiran = $dog_info['cipiran'];
    else $cipiran = 0;
    if(isset($dog_info['kastriran'])) $kastriran = $dog_info['kastriran'];
    else $kastriran = 0;
    if(isset($dog_info['slaganje'])) $slaganje = $dog_info['slaganje'];
    else $slaganje = 0;
    if(isset($dog_info['socijaliziran'])) $socijaliziran = $dog_info['socijaliziran'];
    else $socijaliziran = 0;
    if(isset($dog_info['plah'])) $plah = $dog_info['plah'];
    else $plah = 0;
    if(isset($dog_info['aktivniji'])) $aktivniji = $dog_info['aktivniji'];
    else $aktivniji = 0;
    if(isset($dog_info['manje-aktivni'])) $manje_aktivni = $dog_info['manje-aktivni'];
    else $manje_aktivni = 0;
    if(isset($dog_info['paragraf-1'])) $opis = htmlentities($dog_info['paragraf-1'],ENT_QUOTES,"UTF-8");
    else $opis = '';

    $lang = 1;

    $dog_name = htmlentities($dog_info['article_title'],ENT_QUOTES,"UTF-8");
    $dog_breed=htmlentities($dog_info['pasmina'],ENT_QUOTES,"UTF-8");
    $dog_size=htmlentities($dog_info['velicina'],ENT_QUOTES,"UTF-8");
    $dog_id = $dog_info['id'];

    $dogData = [];
    $stmt = $conn->prepare("SELECT * FROM `dogs` WHERE `id` = ? ");
    $stmt->bind_param("i", $dog_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $dogData = $row;
        }
    }
    $stmt = $conn->prepare("SELECT * FROM `dog_imgs` WHERE `dog_id` = ? ORDER BY `id` ASC");
    $stmt->bind_param("i", $dog_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    if($result->num_rows > 0){
        $x = 0;
        while($row = $result->fetch_assoc()){
            $dogData['imgs'][$x] = $row;
            $x++;
        }
    }
    

    /* uploadaju se nove slike */
    if($dog_photo['name'][0] !== ''){
        // Obriši staru sliku
        foreach($dogData['imgs'] as $delete_img){
            if(file_exists(WEB_ROOT.'images/animals/'.$delete_img['img'])) unlink(WEB_ROOT.'images/animals/'.$delete_img['img']);
            if(file_exists(WEB_ROOT.'images/animals/original/'.$delete_img['img'])) unlink(WEB_ROOT.'images/animals/original/'.$delete_img['img']);
            if(file_exists(WEB_ROOT.'images/animals/thumbs/'.$delete_img['img'])) unlink(WEB_ROOT.'images/animals/thumbs/'.$delete_img['img']);

            $change_front_name = str_replace('.webp','',$delete_img['img']);
            /* traženje ekstenzije */
            if(file_exists(WEB_ROOT.'images/animals/original/'.$change_front_name.'.png')) $full_change_front_name = $change_front_name.'.png';
            if(file_exists(WEB_ROOT.'images/animals/original/'.$change_front_name.'.jpg')) $full_change_front_name = $change_front_name.'.jpg';
            if(file_exists(WEB_ROOT.'images/animals/original/'.$change_front_name.'.jpeg')) $full_change_front_name = $change_front_name.'.jpeg';
            if(file_exists(WEB_ROOT.'images/animals/original/'.$change_front_name.'.gif')) $full_change_front_name = $change_front_name.'.gif';
            if(file_exists(WEB_ROOT.'images/animals/original/'.$change_front_name.'.webp')) $full_change_front_name = $change_front_name.'.webp';
            if(file_exists(WEB_ROOT.'images/animals/original/'.$full_change_front_name)) unlink(WEB_ROOT.'images/animals/original/'.$full_change_front_name);
        }

        $stmt = $conn->prepare("DELETE FROM `dog_imgs` WHERE `dog_id` = ?");
        $stmt->bind_param("i", $dog_id);
        $stmt->execute();
        $stmt->free_result();

        if($dog_info['answer'] == '') $answer = 0;
        else $answer = (int) $dog_info['answer'];

        for($i = 0; $i < count($dog_photo['name']); $i++){

            if($answer == $i){
                $original_image_path = WEB_ROOT."images/animals/original/".$dog_id.'-'.$dog_photo['name'][$i];
                $file = $dog_photo['name'][$i];
                $tmp_file = $dog_photo['tmp_name'][$i];
                $img_name = pathinfo($file, PATHINFO_FILENAME);
                move_uploaded_file($tmp_file, $original_image_path);
        
                $frontImage = $dog_id.'-'.$img_name.'.webp';
                $webp_image_path = WEB_ROOT."images/animals/".$frontImage;
        
                resize_image($original_image_path, $webp_image_path, 830, 475, ZEBRA_IMAGE_BOXED);
            
                $stmt = $conn->prepare("INSERT INTO `dog_imgs` (dog_id, img, front) VALUES (?,?,1)");
                $stmt->bind_param("is", $dog_id, $frontImage);
                $stmt->execute();
            } else {
                $original_image_path = WEB_ROOT."images/animals/original/".$dog_id.'-'.$dog_photo['name'][$i];            
                $file = $dog_photo['name'][$i];
                $tmp_file = $dog_photo['tmp_name'][$i];
                $img_name = pathinfo($file, PATHINFO_FILENAME);
                move_uploaded_file($tmp_file, $original_image_path);
                
                $thumb_path = WEB_ROOT."images/animals/thumbs/".$dog_id."-".$dog_photo['name'][$i];
                resize_image($original_image_path, $thumb_path, 350, 260, ZEBRA_IMAGE_BOXED);

                $uploadimg = $dog_id.'-'.$dog_photo['name'][$i];

                $stmt = $conn->prepare("INSERT INTO `dog_imgs` (dog_id, img, front) VALUES (?,?,0)");
                $stmt->bind_param("is", $dog_id, $uploadimg);
                $stmt->execute();
            }

            
        }
    } elseif($dog_info['answer'] != ''){
        $answer = (int) $dog_info['answer'];
        $y = 0;
        
        foreach($dogData['imgs'] as $deleteimages){
            if($deleteimages['front'] == 1){ 
                $change_front_name = str_replace('.webp','',$deleteimages['img']);
                /* traženje ekstenzije */
                if(file_exists(WEB_ROOT.'images/animals/original/'.$change_front_name.'.png')) $full_change_front_name = $change_front_name.'.png';
                if(file_exists(WEB_ROOT.'images/animals/original/'.$change_front_name.'.jpg')) $full_change_front_name = $change_front_name.'.jpg';
                if(file_exists(WEB_ROOT.'images/animals/original/'.$change_front_name.'.jpeg')) $full_change_front_name = $change_front_name.'.jpeg';
                if(file_exists(WEB_ROOT.'images/animals/original/'.$change_front_name.'.gif')) $full_change_front_name = $change_front_name.'.gif';
                if(file_exists(WEB_ROOT.'images/animals/original/'.$change_front_name.'.webp')) $full_change_front_name = $change_front_name.'.webp';
                /* updateanje baze da front nije više front */
                $stmt = $conn->prepare("UPDATE `dog_imgs` SET `img` = ?, `front` = 0 WHERE `dog_id` = ? AND `front` = 1");
                $stmt->bind_param("si", $full_change_front_name, $dog_id);
                $stmt->execute();
                $stmt->free_result();

                /* brisanje stare front slike */
                if(file_exists(WEB_ROOT.'images/animals/'.$deleteimages['img'])){
                    $thumb_path = WEB_ROOT."images/animals/thumbs/".$full_change_front_name;
                    resize_image(WEB_ROOT.'images/animals/'.$deleteimages['img'], $thumb_path, 350, 260, ZEBRA_IMAGE_BOXED); 
                    unlink(WEB_ROOT.'images/animals/'.$deleteimages['img']);
                    break;
                }
            }
        }
        foreach($dogData['imgs'] as $images){
            if($y == $answer){
                $img_id = $images['id'];
                $original_image_path = WEB_ROOT."images/animals/original/".$images['img'];
                $img_name = pathinfo($images['img'], PATHINFO_FILENAME);

                unlink(WEB_ROOT.'images/animals/thumbs/'.$images['img']);

                $frontImage = $img_name.'.webp';
                $webp_image_path = WEB_ROOT."images/animals/".$frontImage;
        
                resize_image($original_image_path, $webp_image_path, 830, 475, ZEBRA_IMAGE_BOXED);
            
                $stmt = $conn->prepare("UPDATE `dog_imgs` SET `front` = 1, `img` = ? WHERE `id` = ?");
                $stmt->bind_param("si", $frontImage, $img_id);
                $stmt->execute();
                
                break;
            } else{
                $y++;
            }
        }
    }

    $animal_url = parseForSEO($dog_name).'%';
    $url = parseForSEO($dog_name);
    $stmt = $conn->prepare("SELECT * FROM `dogs` WHERE `animal_link` LIKE ? AND `deleted` = 0 ORDER BY `animal_link` DESC LIMIT 1");
    $stmt->bind_param("s", $animal_url);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    $row = $result->fetch_assoc();
    if(!empty($row)) $url = parseForSEO($dog_name.'1');

    $stmt = $conn->prepare("UPDATE `dogs` SET `name` = ?, `spol` = ?, `dob` = ?, `cijepljen` = ?, `cipiran` = ?, `kastriran` = ?, `slaganje` = ?, `socijaliziran` = ?, `plah` = ?, `aktivniji` = ?, `manje_aktivni` = ?, `opis` = ?, `animal_link` = ?, `pasmina` = ?, `velicina` = ? WHERE `id` = ?");
    if(!$stmt->bind_param("sssiiiiiiiissssi", $dog_name, $spol, $dob, $cijepljen, $cipiran, $kastriran, $slaganje, $socijaliziran, $plah, $aktivniji, $manje_aktivni, $opis, $url, $dog_breed, $dog_size, $dog_id)) $stmt->error;
    $stmt->execute();
    return true;
}

function updateCat($dog_info, $dog_photo){
    global $conn;
    var_dump($dog_info);
    if(isset($dog_info['spol'][0])) $spol = $dog_info['spol'][0];
    if(isset($dog_info['dob'][0])) $dob = $dog_info['dob'][0];
    if(isset($dog_info['cijepljen'])) $cijepljen = $dog_info['cijepljen'];
    else $cijepljen = 0;
    if(isset($dog_info['cipiran'])) $cipiran = $dog_info['cipiran'];
    else $cipiran = 0;
    if(isset($dog_info['kastriran'])) $kastriran = $dog_info['kastriran'];
    else $kastriran = 0;
    if(isset($dog_info['slaganje'])) $slaganje = $dog_info['slaganje'];
    else $slaganje = 0;
    if(isset($dog_info['socijaliziran'])) $socijaliziran = $dog_info['socijaliziran'];
    else $socijaliziran = 0;
    if(isset($dog_info['plah'])) $plah = $dog_info['plah'];
    else $plah = 0;
    if(isset($dog_info['aktivniji'])) $aktivniji = $dog_info['aktivniji'];
    else $aktivniji = 0;
    if(isset($dog_info['manje-aktivni'])) $manje_aktivni = $dog_info['manje-aktivni'];
    else $manje_aktivni = 0;
    if(isset($dog_info['paragraf-1'])) $opis = htmlentities($dog_info['paragraf-1'],ENT_QUOTES,"UTF-8");
    else $opis = '';

    $lang = 1;

    $dog_name = htmlentities($dog_info['article_title'],ENT_QUOTES,"UTF-8");
    $dog_breed=htmlentities($dog_info['pasmina'],ENT_QUOTES,"UTF-8");
    $dog_size=htmlentities($dog_info['velicina'],ENT_QUOTES,"UTF-8");
    $dog_id = $dog_info['id'];

    $dogData = [];
    $stmt = $conn->prepare("SELECT * FROM `cats` WHERE `id` = ? ");
    $stmt->bind_param("i", $dog_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $dogData = $row;
        }
    }
    $stmt = $conn->prepare("SELECT * FROM `cat_imgs` WHERE `cat_id` = ? ORDER BY `id` ASC");
    $stmt->bind_param("i", $dog_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    if($result->num_rows > 0){
        $x = 0;
        while($row = $result->fetch_assoc()){
            $dogData['imgs'][$x] = $row;
            $x++;
        }
    }
    

    /* uploadaju se nove slike */
    if($dog_photo['name'][0] !== ''){
        // Obriši staru sliku
        foreach($dogData['imgs'] as $delete_img){
            if(file_exists(WEB_ROOT.'images/animals/'.$delete_img['img'])) unlink(WEB_ROOT.'images/animals/'.$delete_img['img']);
            if(file_exists(WEB_ROOT.'images/animals/original/'.$delete_img['img'])) unlink(WEB_ROOT.'images/animals/original/'.$delete_img['img']);
            if(file_exists(WEB_ROOT.'images/animals/thumbs/'.$delete_img['img'])) unlink(WEB_ROOT.'images/animals/thumbs/'.$delete_img['img']);

            $change_front_name = str_replace('.webp','',$delete_img['img']);
            /* traženje ekstenzije */
            if(file_exists(WEB_ROOT.'images/animals/original/'.$change_front_name.'.png')) $full_change_front_name = $change_front_name.'.png';
            if(file_exists(WEB_ROOT.'images/animals/original/'.$change_front_name.'.jpg')) $full_change_front_name = $change_front_name.'.jpg';
            if(file_exists(WEB_ROOT.'images/animals/original/'.$change_front_name.'.jpeg')) $full_change_front_name = $change_front_name.'.jpeg';
            if(file_exists(WEB_ROOT.'images/animals/original/'.$change_front_name.'.gif')) $full_change_front_name = $change_front_name.'.gif';
            if(file_exists(WEB_ROOT.'images/animals/original/'.$change_front_name.'.webp')) $full_change_front_name = $change_front_name.'.webp';
            if(file_exists(WEB_ROOT.'images/animals/original/'.$full_change_front_name)) unlink(WEB_ROOT.'images/animals/original/'.$full_change_front_name);
        }

        $stmt = $conn->prepare("DELETE FROM `dog_imgs` WHERE `dog_id` = ?");
        $stmt->bind_param("i", $dog_id);
        $stmt->execute();
        $stmt->free_result();

        if($dog_info['answer'] == '') $answer = 0;
        else $answer = (int) $dog_info['answer'];

        for($i = 0; $i < count($dog_photo['name']); $i++){

            if($answer == $i){
                $original_image_path = WEB_ROOT."images/animals/original/".$dog_id.'-'.$dog_photo['name'][$i];
                $file = $dog_photo['name'][$i];
                $tmp_file = $dog_photo['tmp_name'][$i];
                $img_name = pathinfo($file, PATHINFO_FILENAME);
                move_uploaded_file($tmp_file, $original_image_path);
        
                $frontImage = $dog_id.'-'.$img_name.'.webp';
                $webp_image_path = WEB_ROOT."images/animals/".$frontImage;
        
                resize_image($original_image_path, $webp_image_path, 830, 475, ZEBRA_IMAGE_BOXED);
            
                $stmt = $conn->prepare("INSERT INTO `dog_imgs` (dog_id, img, front) VALUES (?,?,1)");
                $stmt->bind_param("is", $dog_id, $frontImage);
                $stmt->execute();
            } else {
                $original_image_path = WEB_ROOT."images/animals/original/".$dog_id.'-'.$dog_photo['name'][$i];            
                $file = $dog_photo['name'][$i];
                $tmp_file = $dog_photo['tmp_name'][$i];
                $img_name = pathinfo($file, PATHINFO_FILENAME);
                move_uploaded_file($tmp_file, $original_image_path);
                
                $thumb_path = WEB_ROOT."images/animals/thumbs/".$dog_id."-".$dog_photo['name'][$i];
                resize_image($original_image_path, $thumb_path, 350, 260, ZEBRA_IMAGE_BOXED);

                $uploadimg = $dog_id.'-'.$dog_photo['name'][$i];

                $stmt = $conn->prepare("INSERT INTO `dog_imgs` (dog_id, img, front) VALUES (?,?,0)");
                $stmt->bind_param("is", $dog_id, $uploadimg);
                $stmt->execute();
            }

            
        }
    } elseif($dog_info['answer'] != ''){
        $answer = (int) $dog_info['answer'];
        $y = 0;
        
        foreach($dogData['imgs'] as $deleteimages){
            if($deleteimages['front'] == 1){ 
                $change_front_name = str_replace('.webp','',$deleteimages['img']);
                /* traženje ekstenzije */
                if(file_exists(WEB_ROOT.'images/animals/original/'.$change_front_name.'.png')) $full_change_front_name = $change_front_name.'.png';
                if(file_exists(WEB_ROOT.'images/animals/original/'.$change_front_name.'.jpg')) $full_change_front_name = $change_front_name.'.jpg';
                if(file_exists(WEB_ROOT.'images/animals/original/'.$change_front_name.'.jpeg')) $full_change_front_name = $change_front_name.'.jpeg';
                if(file_exists(WEB_ROOT.'images/animals/original/'.$change_front_name.'.gif')) $full_change_front_name = $change_front_name.'.gif';
                if(file_exists(WEB_ROOT.'images/animals/original/'.$change_front_name.'.webp')) $full_change_front_name = $change_front_name.'.webp';
                /* updateanje baze da front nije više front */
                $stmt = $conn->prepare("UPDATE `dog_imgs` SET `img` = ?, `front` = 0 WHERE `dog_id` = ? AND `front` = 1");
                $stmt->bind_param("si", $full_change_front_name, $dog_id);
                $stmt->execute();
                $stmt->free_result();

                /* brisanje stare front slike */
                if(file_exists(WEB_ROOT.'images/animals/'.$deleteimages['img'])){
                    $thumb_path = WEB_ROOT."images/animals/thumbs/".$full_change_front_name;
                    resize_image(WEB_ROOT.'images/animals/'.$deleteimages['img'], $thumb_path, 350, 260, ZEBRA_IMAGE_BOXED); 
                    unlink(WEB_ROOT.'images/animals/'.$deleteimages['img']);
                    break;
                }
            }
        }
        foreach($dogData['imgs'] as $images){
            if($y == $answer){
                $img_id = $images['id'];
                $original_image_path = WEB_ROOT."images/animals/original/".$images['img'];
                $img_name = pathinfo($images['img'], PATHINFO_FILENAME);

                unlink(WEB_ROOT.'images/animals/thumbs/'.$images['img']);
                
                $frontImage = $img_name.'.webp';
                $webp_image_path = WEB_ROOT."images/animals/".$frontImage;
        
                resize_image($original_image_path, $webp_image_path, 830, 475, ZEBRA_IMAGE_BOXED);
            
                $stmt = $conn->prepare("UPDATE `dog_imgs` SET `front` = 1, `img` = ? WHERE `id` = ?");
                $stmt->bind_param("si", $frontImage, $img_id);
                $stmt->execute();
                
                break;
            } else{
                $y++;
            }
        }
    }

    $animal_url = parseForSEO($dog_name).'%';
    $url = parseForSEO($dog_name);
    $stmt = $conn->prepare("SELECT * FROM `cats` WHERE `animal_link` LIKE ? AND `deleted` = 0 ORDER BY `animal_link` DESC LIMIT 1");
    $stmt->bind_param("s", $animal_url);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    $row = $result->fetch_assoc();
    if(!empty($row)) $url = parseForSEO($dog_name.'1');

    $stmt = $conn->prepare("UPDATE `dogs` SET `name` = ?, `spol` = ?, `dob` = ?, `cijepljen` = ?, `cipiran` = ?, `kastriran` = ?, `slaganje` = ?, `socijaliziran` = ?, `plah` = ?, `aktivniji` = ?, `manje_aktivni` = ?, `opis` = ?, `animal_link` = ?, `pasmina` = ?, `velicina` = ? WHERE `id` = ?");
    if(!$stmt->bind_param("sssiiiiiiiissssi", $dog_name, $spol, $dob, $cijepljen, $cipiran, $kastriran, $slaganje, $socijaliziran, $plah, $aktivniji, $manje_aktivni, $opis, $url, $dog_breed, $dog_size, $dog_id)) $stmt->error;
    $stmt->execute();
    return true;
}

function deleteDog($dog_id){
    global $conn;

    $delete_imgs = [];
    $id = (int)mysqli_real_escape_string($conn, trim($dog_id));
    $stmt = $conn->prepare("SELECT * FROM `dog_imgs` WHERE `dog_id` = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $delete_imgs[] = $row;
        }
    }

    foreach($delete_imgs as $img){
        if($img['front'] == 1){
            $change_front_name = str_replace('.webp','',$img['img']);
            /* traženje ekstenzije */
            if(file_exists(WEB_ROOT.'images/animals/original/'.$change_front_name.'.png')) $full_change_front_name = $change_front_name.'.png';
            if(file_exists(WEB_ROOT.'images/animals/original/'.$change_front_name.'.jpg')) $full_change_front_name = $change_front_name.'.jpg';
            if(file_exists(WEB_ROOT.'images/animals/original/'.$change_front_name.'.jpeg')) $full_change_front_name = $change_front_name.'.jpeg';
            if(file_exists(WEB_ROOT.'images/animals/original/'.$change_front_name.'.gif')) $full_change_front_name = $change_front_name.'.gif';
            if(file_exists(WEB_ROOT.'images/articles/original/'.$change_front_name.'.webp')) $full_change_front_name = $change_front_name.'.webp';

            if(file_exists(WEB_ROOT.'images/animals/original/'.$full_change_front_name)) unlink(WEB_ROOT.'images/animals/original/'.$full_change_front_name);
            if(file_exists(WEB_ROOT.'images/animals/'.$img['img'])) unlink(WEB_ROOT.'images/animals/'.$img['img']);

        } else {
            if(file_exists(WEB_ROOT.'images/animals/original/'.$img['img'])) unlink(WEB_ROOT.'images/animals/original/'.$img['img']);
            if(file_exists(WEB_ROOT.'images/animals/thumbs/'.$img['img'])) unlink(WEB_ROOT.'images/animals/thumbs/'.$img['img']);
        }
    }

    /* brisanje slika iz baze */
    $stmt = $conn->prepare("DELETE FROM `dog_imgs` WHERE `dog_id` = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    $stmt = $conn->prepare("UPDATE `dogs` SET `deleted` = 1 WHERE `id` = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
}

function deleteCat($dog_id){
    global $conn;

    $delete_imgs = [];
    $id = (int)mysqli_real_escape_string($conn, trim($dog_id));
    $stmt = $conn->prepare("SELECT * FROM `cat_imgs` WHERE `cat_id` = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $delete_imgs[] = $row;
        }
    }

    /* brisanje slika u folderu images/articles */
    foreach($delete_imgs as $img){
        if($img['front'] == 1){
            $change_front_name = str_replace('.webp','',$img['img']);
            /* traženje ekstenzije */
            if(file_exists(WEB_ROOT.'images/animals/original/'.$change_front_name.'.png')) $full_change_front_name = $change_front_name.'.png';
            if(file_exists(WEB_ROOT.'images/animals/original/'.$change_front_name.'.jpg')) $full_change_front_name = $change_front_name.'.jpg';
            if(file_exists(WEB_ROOT.'images/animals/original/'.$change_front_name.'.jpeg')) $full_change_front_name = $change_front_name.'.jpeg';
            if(file_exists(WEB_ROOT.'images/animals/original/'.$change_front_name.'.gif')) $full_change_front_name = $change_front_name.'.gif';
            if(file_exists(WEB_ROOT.'images/articles/original/'.$change_front_name.'.webp')) $full_change_front_name = $change_front_name.'.webp';

            if(file_exists(WEB_ROOT.'images/animals/original/'.$full_change_front_name)) unlink(WEB_ROOT.'images/animals/original/'.$full_change_front_name);
            if(file_exists(WEB_ROOT.'images/animals/'.$img['img'])) unlink(WEB_ROOT.'images/animals/'.$img['img']);

        } else {
            if(file_exists(WEB_ROOT.'images/animals/original/'.$img['img'])) unlink(WEB_ROOT.'images/animals/original/'.$img['img']);
            if(file_exists(WEB_ROOT.'images/animals/thumbs/'.$img['img'])) unlink(WEB_ROOT.'images/animals/thumbs/'.$img['img']);
        }
    }

    /* brisanje slika iz baze */
    $stmt = $conn->prepare("DELETE FROM `cat_imgs` WHERE `cat_id` = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    $stmt = $conn->prepare("UPDATE `cats` SET `deleted` = 1 WHERE `id` = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
}

function selectAboutUs(){
    global $conn;

    $content = [];

    $stmt = $conn->prepare("SELECT * FROM `aboutus` WHERE `id` = 1");
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    while($row = $result->fetch_assoc()){
        $content = $row;
    }

    $content['title1'] = html_entity_decode($content['title1'], ENT_QUOTES, "UTF-8");
    $content['title2'] = html_entity_decode($content['title2'], ENT_QUOTES, "UTF-8");
    $content['content1'] = html_entity_decode($content['content1'], ENT_QUOTES, "UTF-8");
    $content['content2'] = html_entity_decode($content['content2'], ENT_QUOTES, "UTF-8");
    if($content['img1'] != '0') $content['img1'] = WWW_ROOT."images/aboutus/".$content['img1'];
    if($content['img2'] != '0') $content['img2'] = WWW_ROOT."images/aboutus/".$content['img2'];

    return $content;
}

function updateAboutUs($content, $images){
    global $conn;

    $title1 = htmlentities($content['article_title'],ENT_QUOTES,"UTF-8");
    $title2 = htmlentities($content['article_title2'],ENT_QUOTES,"UTF-8");
    $content1 = htmlentities($content['sadrzaj1'],ENT_QUOTES,"UTF-8");
    $content2 = htmlentities($content['sadrzaj2'],ENT_QUOTES,"UTF-8");

    if($images['name'][0] !== ''){
        $aboutusData = [];
        $stmt = $conn->prepare("SELECT * FROM `aboutus` WHERE `id` = 1");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->free_result();
        while($row = $result->fetch_assoc()){
            $aboutusData = $row;
        }
        // Obriši staru sliku
        if(file_exists(WEB_ROOT.'images/aboutus/'.$aboutusData['img1'])) unlink(WEB_ROOT.'images/aboutus/'.$aboutusData['img1']);
        if(file_exists(WEB_ROOT.'images/aboutus/'.$aboutusData['img2'])) unlink(WEB_ROOT.'images/aboutus/'.$aboutusData['img2']);

        for($i = 0; $i < count($images['name']); $i++){
            $original_image_path = WEB_ROOT."images/aboutus/".$images['name'][$i];            
            $file = $images['name'][$i];
            $tmp_file = $images['tmp_name'][$i];
            $img_name = pathinfo($file, PATHINFO_FILENAME);
            move_uploaded_file($tmp_file, $original_image_path);
            resize_image($original_image_path, $original_image_path, 540, 390, ZEBRA_IMAGE_BOXED);
            $images_names[] = $images['name'][$i];
        }

        if(!isset($images['name'][1])) $images_names[1] = '0';

        $stmt = $conn->prepare("UPDATE `aboutus` SET `title1` = ?, `title2` = ?, `content1` = ?, `content2` = ?, `img1` = ?, `img2` = ? WHERE `id` = 1");
        $stmt->bind_param("ssssss",$title1, $title2, $content1, $content2, $images_names[0], $images_names[1]);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("UPDATE `aboutus` SET `title1` = ?, `title2` = ?, `content1` = ?, `content2` = ? WHERE `id` = 1");
        $stmt->bind_param("ssss",$title1, $title2, $content1, $content2);
        $stmt->execute();
    }
}

function selectDonations(){
    global $conn;

    $content = [];

    $stmt = $conn->prepare("SELECT * FROM `donations`");
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    while($row = $result->fetch_assoc()){
        if($row['type'] == 1) $content['title'] = html_entity_decode($row['content'], ENT_QUOTES, "UTF-8");
        if($row['type'] == 2) $content['sadrzaj'] = html_entity_decode($row['content'], ENT_QUOTES, "UTF-8");
        if($row['type'] == 3) $content['imgs'][] = WWW_ROOT."images/donacije/predmeti/".$row['content'];
        if($row['type'] == 4) $content['racun'] = WWW_ROOT."images/donacije/racun/".$row['content'];
    }

    return $content;

}

function updateDonations($content, $donations_images, $devizni_racun){
    global $conn;

    $donationData = [];
    $donationData[0]['content'] = htmlentities($content['article_title'],ENT_QUOTES,"UTF-8");
    $donationData[0]['type'] = 1;
    $donationData[1]['content'] = htmlentities($content['sadrzaj1'],ENT_QUOTES,"UTF-8");
    $donationData[1]['type'] = 2;

    $stmt = $conn->prepare("UPDATE `donations` SET `content` = ? WHERE `type` = ?");
    foreach($donationData as $data){
        $stmt->bind_param("ss", $data['content'], $data['type']);
        $stmt->execute();
    }
    

    if($donations_images['name'][0] !== ''){
        $donationsImgs = [];
        $stmt = $conn->prepare("SELECT * FROM `donations` WHERE `type` = 3");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->free_result();
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $donationsImgs[] = $row;
            }
            $stmt = $conn->prepare("DELETE FROM `donations` WHERE `type` = 3");
            $stmt->execute();

            foreach($donationsImgs as $delete) if(file_exists(WEB_ROOT.'images/donacije/predmeti/'.$delete['content'])) unlink(WEB_ROOT.'images/donacije/predmeti/'.$delete['content']);
        }
        

        for($i = 0; $i < count($donations_images['name']); $i++){
            $original_image_path = WEB_ROOT."images/donacije/predmeti/".$donations_images['name'][$i];            
            $file = $donations_images['name'][$i];
            $tmp_file = $donations_images['tmp_name'][$i];
            $img_name = pathinfo($file, PATHINFO_FILENAME);
            move_uploaded_file($tmp_file, $original_image_path);
            resize_image($original_image_path, $original_image_path, 500, 500, ZEBRA_IMAGE_BOXED);
            $images_names[] = $donations_images['name'][$i];
        }


        $stmt = $conn->prepare("INSERT INTO `donations` (`content`,`type`) VALUES (?,3)");
        foreach($images_names as $name){
            $stmt->bind_param("s",$name);
            $stmt->execute();
        }
        
    }

    if($devizni_racun['name'][0] !== ''){
        $donationsImgs = [];
        $stmt = $conn->prepare("SELECT * FROM `donations` WHERE `type` = 4");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->free_result();
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $donationsImgs[] = $row;
            }
            $stmt = $conn->prepare("DELETE FROM `donations` WHERE `type` = 4");
            $stmt->execute();
            foreach($donationsImgs as $delete) if(file_exists(WEB_ROOT.'images/donacije/racun/'.$delete['content'])) unlink(WEB_ROOT.'images/donacije/racun/'.$delete['content']);
        }
        

        for($i = 0; $i < count($devizni_racun['name']); $i++){
            $original_image_path = WEB_ROOT."images/donacije/racun/".$devizni_racun['name'][$i];            
            $file = $devizni_racun['name'][$i];
            $tmp_file = $devizni_racun['tmp_name'][$i];
            $img_name = pathinfo($file, PATHINFO_FILENAME);
            move_uploaded_file($tmp_file, $original_image_path);
            resize_image($original_image_path, $original_image_path, 540, 390, ZEBRA_IMAGE_BOXED);
            $images_names2[] = $devizni_racun['name'][$i];
        }


        $stmt = $conn->prepare("INSERT INTO `donations` (`content`,`type`) VALUES (?,4)");
        foreach($images_names2 as $name){
            $stmt->bind_param("s",$name);
            $stmt->execute();
        }
        
    }
}

function selectKontakt(){
    global $conn;

    $content = [];

    $stmt = $conn->prepare("SELECT * FROM `contact`");
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    while($row = $result->fetch_assoc()){
        if($row['type'] == 1) $content['title'] = html_entity_decode($row['content'], ENT_QUOTES, "UTF-8");
        if($row['type'] == 2) $content['lokacije'] = html_entity_decode($row['content'], ENT_QUOTES, "UTF-8");
        if($row['type'] == 3) $content['telefon'] = html_entity_decode($row['content'], ENT_QUOTES, "UTF-8");
        if($row['type'] == 4) $content['email'] = html_entity_decode($row['content'], ENT_QUOTES, "UTF-8");
        if($row['type'] == 5) $content['email_form'] = html_entity_decode($row['content'], ENT_QUOTES, "UTF-8");
        if($row['type'] == 6) $content['pass_form'] = html_entity_decode($row['content'], ENT_QUOTES, "UTF-8");
    }

    return $content;

}

function updateKontakt($content){
    global $conn;
    
    $kontaktData = [];
    $i = 0;
    foreach($content as $key => $data){
        if(strcmp($key, 'article_title') == 0){ 
            $kontaktData[$i]['content'] = htmlentities($data, ENT_QUOTES, "UTF-8");
            $kontaktData[$i]['type'] = 1;
        } elseif(strcmp($key, 'lokacije') == 0){ 
            $kontaktData[$i]['content'] = htmlentities($data, ENT_QUOTES, "UTF-8");
            $kontaktData[$i]['type'] = 2;
        } elseif(strcmp($key, 'telefon') == 0){ 
            $kontaktData[$i]['content'] = htmlentities($data, ENT_QUOTES, "UTF-8");
            $kontaktData[$i]['type'] = 3;
        } elseif(strcmp($key, 'email') == 0){ 
            $kontaktData[$i]['content'] = htmlentities($data, ENT_QUOTES, "UTF-8");
            $kontaktData[$i]['type'] = 4;
        } elseif(strcmp($key, 'email-form') == 0){ 
            $kontaktData[$i]['content'] = htmlentities($data, ENT_QUOTES, "UTF-8");
            $kontaktData[$i]['type'] = 5;
        } elseif(strcmp($key, 'pass-form') == 0){ 
            $kontaktData[$i]['content'] = htmlentities($data, ENT_QUOTES, "UTF-8");
            $kontaktData[$i]['type'] = 6;
        }
        $i++;
    }

    $stmt = $conn->prepare("UPDATE `contact` SET `content` = ? WHERE `type` = ?");
    foreach($kontaktData as $data){
        $stmt->bind_param("ss", $data['content'], $data['type']);
        $stmt->execute();
    }
}

function selectRotatorSlide($slide_id){
    global $conn;

    $data = [];
    $sql="SELECT * FROM `glavni_rotator` AS a LEFT JOIN `glavni_rotator_img` AS b ON a.slide_id = b.slide_id WHERE a.slide_id = '".$slide_id."' LIMIT 2";
	$result = $conn->query($sql);
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()){
			foreach($row as $key => $value) {
			$data[$row['lang']][$key] = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
			}
		}
		return $data;
	}
	return false;

}

function selectRotator($lang='1'){
	global $conn;

	$stmt = $conn->prepare("SELECT *
							FROM
								`glavni_rotator` AS a
								LEFT JOIN `glavni_rotator_img` AS b ON a.`slide_id` = b.`slide_id`
							WHERE 
								a.`lang` = ?");
    if(!$stmt->bind_param("i", $lang)) $stmt->error;
    $stmt->execute();
	$result = $stmt->get_result();
	$i = 0;
	while($row = $result->fetch_assoc()){
		$rotator_content[$i]['id'] = $row['id'];
		$rotator_content[$i]['slide_id'] = $row['slide_id'];
		$rotator_content[$i]['title'] = $row['title'];
		$rotator_content[$i]['button'] = html_entity_decode($row['button'], ENT_QUOTES);
		$rotator_content[$i]['content'] = $row['content'];
		$rotator_content[$i]['img'] = $row['img'];
		$i++;
	}
	return $rotator_content;
}

function selectFooter(){
    global $conn;

    $content = [];

    $stmt = $conn->prepare("SELECT * FROM `footer`");
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    while($row = $result->fetch_assoc()){
        $content['title'] = html_entity_decode($row['title'], ENT_QUOTES, "UTF-8");
        $content['content'] = html_entity_decode($row['content'], ENT_QUOTES, "UTF-8");
    }

    return $content;
}

function updateFooter($content){
    global $conn;

    $title = htmlentities($content['article_title'],ENT_QUOTES,"UTF-8");
    $sadrzaj = htmlentities($content['sadrzaj1'],ENT_QUOTES,"UTF-8");

    $stmt = $conn->prepare("UPDATE `footer` SET `title` = ?, `content` = ? WHERE `id` = 1");
    $stmt->bind_param("ss", $title, $sadrzaj);
    $stmt->execute();
}

function selectMiddle(){
    global $conn;

    $content = [];

    $stmt = $conn->prepare("SELECT * FROM `middle`");
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    while($row = $result->fetch_assoc()){
        $content['title'] = html_entity_decode($row['title'], ENT_QUOTES, "UTF-8");
        $content['content'] = html_entity_decode($row['content'], ENT_QUOTES, "UTF-8");
        $content['link'] = urldecode($row['link']);
        $content['img'] = WWW_ROOT.'images/'.$row['img'];
    }

    return $content;
}

function updateMiddle($content, $image){
    global $conn;

    $title = htmlentities($content['article_title'],ENT_QUOTES,"UTF-8");
    $sadrzaj = htmlentities($content['sadrzaj1'],ENT_QUOTES,"UTF-8");
    $link = urlencode($content['article_title2']);


    if($image['name'][0] !== ''){
        $middleImg = [];
        $stmt = $conn->prepare("SELECT * FROM `middle` WHERE `id` = 1");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->free_result();
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()) $middleImg = $row;
            if(file_exists(WEB_ROOT.'images/'.$middleImg['img'])) unlink(WEB_ROOT.'images/'.$middleImg['img']);
        }
        

        $original_image_path = WEB_ROOT."images/".$image['name'][0];            
        $file = $image['name'][0];
        $tmp_file = $image['tmp_name'][0];
        $img_name = pathinfo($file, PATHINFO_FILENAME);
        move_uploaded_file($tmp_file, $original_image_path);
        resize_image($original_image_path, $original_image_path, 1580, 455, ZEBRA_IMAGE_CROP_CENTER);
        $images_name = $image['name'][0];


        $stmt = $conn->prepare("UPDATE `middle` SET `img` = ? WHERE `id` = 1 ");
        $stmt->bind_param("s",$images_name);
        $stmt->execute();
    }

    $stmt = $conn->prepare("UPDATE `middle` SET `title` = ?, `content` = ?, `link` = ? WHERE `id` = 1 ");
    $stmt->bind_param("sss",$title, $sadrzaj, $link);
    $stmt->execute();
}

function selectAllContactMsgs(){
    global $conn;

    $content = [];

    $stmt = $conn->prepare("UPDATE `contact_msgs` SET `read` = 1 WHERE `read` = 0");
    $stmt->execute();
    $stmt->free_result();

    $stmt = $conn->prepare("SELECT * FROM `contact_msgs`");
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $row['date_created'] = date("d.m.Y h:i", strtotime($row['date_created']));
            $content[] = $row;
        }
    } else return false;
    
    return $content;
}

function selectAllUnreadMsgs(){
    global $conn;

    $content = [];

    $stmt = $conn->prepare("SELECT COUNT(*) as `rows` FROM `contact_msgs` WHERE `read` = 0");
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $content = $row;
        }
    } else return false;
    
    return $content;
}

function deleteContactMsg($id){
    global $conn;

    $stmt = $conn->prepare("DELETE FROM `contact_msgs` WHERE `id` = ?");
    $stmt->bind_param("i", $id);
    if($stmt->execute()) return true;
    return false;
}

?>