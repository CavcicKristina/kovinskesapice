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

function getAllArticles($lang){
	global $conn;
     
	$sql = "SELECT * FROM articles AS a LEFT JOIN article_content AS b ON a.id = b.article_id WHERE a.lang = ".$conn->real_escape_string($lang)." AND b.position = 0 AND show_frontend = 1";
	$result = $conn->query($sql);
    $i=0;
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){	
            $content[$i]['id'] = $row['article_id'];
            $content[$i]['title'] = $row['title'];
            $content[$i]['author'] = $row['author'];
            $content[$i]['img'] = $row['content'];
            $content[$i]['active'] = $row['active'];
            $content[$i]['article_link'] = $row['article_link'];
            $content[$i]['date_created'] = date("m-d-Y", strtotime($row['date_created']));
            $i++;
        }
        return $content;
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

function uploadArticle($lang, $title, $author, $slider_img, $cheditor_content, $arr_img, $conn, $main_img, $header){
    if(empty($main_img)) $main_img = 0;

    #GLAVNI DIO ARTICLE
    #Dobijemo nazad article_id 
    #dodati aktive 1 da se koristi
    $stmt = $conn->prepare("INSERT INTO articles (title, header, lang, author, active, date_created) VALUES (?,?,?,?,0,NOW())");
    if(!$stmt->bind_param("ssis", $title, $header, $lang, $author)) $stmt->error;
    $stmt->execute();
    $article_id = $conn->insert_id;

    /* UPLOAD SHOW_FRONTEND SLIKE ZA ČLANAK */
    $stmt = $conn->prepare("INSERT INTO article_content (article_id, content, content_type, position, show_frontend) VALUES (?,?,1,0,1)");
    if(!$stmt->bind_param("is", $article_id, $slider_img['name'][0])) $stmt->error;
    $stmt->execute();

    $original_image_path = WEB_ROOT."images/content/article_uploads/".$article_id."-main-".$slider_img['name'][0];
    $file = $slider_img['name'][0];
    $tmp_file = $slider_img['tmp_name'][0];
    move_uploaded_file($tmp_file, $original_image_path);
    resize_image($original_image_path, $original_image_path, 600, 400, ZEBRA_IMAGE_BOXED);

    /* CKEDITOR CONTENT INSERT */
    $paragraf = htmlentities($cheditor_content,ENT_QUOTES,"UTF-8");
    $stmt = $conn->prepare("INSERT INTO article_content (article_id, content, content_type, position, show_frontend) VALUES (?,?,3,0,0)");
    if(!$stmt->bind_param("is", $article_id, $paragraf)) $stmt->error;
    $stmt->execute();

}

function uploadArticleContent($conn, $article_id, $content_type, $position, $content, $main_content){
    $stmt = $conn->prepare("INSERT INTO article_content (article_id, content_type, position, content, show_frontend) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiisi",  $article_id, $content_type, $position, $content, $main_content);
    $stmt->execute();
}

function updateArticleContent($conn, $article_id, $content_type, $position, $content, $main_content){
    $stmt = $conn->prepare("UPDATE article_content SET content = ?, show_frontend = ? WHERE article_id = ? AND content_type = ? AND position = ?");
    $stmt->bind_param("siiii", $content, $main_content, $article_id, $content_type, $position);
    $stmt->execute();
}

function imageUploadArticleSlider($article_id, $img, $folder, $main_img){
    
    $nmb_of_img = count($img['name']);

    for($i = 0; $i < $nmb_of_img; $i++){

        /* moving original file to product images */
        $original_image_path = WEB_ROOT."images/content/".$folder.$article_id."-".$img['name'][$i];
        $file = $img['name'][$i];
        $tmp_file = $img['tmp_name'][$i];
        $img_extension = pathinfo($file, PATHINFO_EXTENSION);
        $img_name = pathinfo($file, PATHINFO_FILENAME);
        move_uploaded_file($tmp_file, $original_image_path);
        
        if($i == $main_img) {
            $thumbnail_path = WEB_ROOT."images/content/article-slider-thumbs/".$article_id."-".$img_name.'-thumbnail-main.'.$img_extension;
            $thumb1 = resize_image($original_image_path, $thumbnail_path, 600, 400, ZEBRA_IMAGE_BOXED);
        }
        $thumbnail_path = WEB_ROOT."images/content/article-slider-thumbs/".$article_id."-".$img_name.'-thumbnail.'.$img_extension;
        $thumb2 = resize_image($original_image_path, $thumbnail_path, 850, 478, ZEBRA_IMAGE_BOXED);
    }
}

function imageUploadArticle($article_id, $img, $conn, $folder){
    /* moving original file to article images */
    $original_image_path = WEB_ROOT."/images/content/".$folder.$article_id."-".$img['name'];
    $file = $img['name'];
    $tmp_file = $img['tmp_name'];
    move_uploaded_file($tmp_file, $original_image_path);
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

function selectArticle($article_id){
    global $conn;
    $data = [];
    $last_position = 1;

    $result = $conn->query("SELECT title, header, lang FROM articles WHERE id = ".$article_id." ");
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $data['title'] = $row['title'];
            $data['header'] = $row['header'];
            $data['lang'] = $row['lang'];
        }
    } else return false;

    $result = $conn->query("SELECT position, content_type, content FROM article_content WHERE article_id = ".$article_id." ORDER BY position ASC");
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            if($row['content_type'] == '3') $row['content'] = html_entity_decode($row['content'], ENT_QUOTES, 'UTF-8');
            $data[] = $row;
            if($row['position'] > $last_position) $last_position = $row['position'];
        }
    } else return false;

    $data['last_position'] = $last_position;

    return $data;
}

function updateArticle($id, $title, $author, $slider_img, $arr_text_video, $arr_img, $main_img, $header){
    global $conn;
    if(empty($main_img)) $main_img = 0;

    $stmt = $conn->prepare("UPDATE articles SET title = ?, header = ? WHERE id = ? ");
    if(!$stmt->bind_param("ssi", $title, $header, $id)) $stmt->error;
    $stmt->execute();

    /* SLIDER UPDATE I INSERT AKO IMA MANJE FOTOGRAFIJA*/
    if(!empty($slider_img['name'][0])){
        /* ovdje traži fotografije u bazi i sprema u arr */
        $delete_imgs_slider = [];
        $id = (int)mysqli_real_escape_string($conn, trim($id));
        $result = $conn->query("SELECT content, id FROM article_content WHERE article_id = $id AND content_type = 1");
        if($result->num_rows > 0){
            $i = 0;
            while($row = $result->fetch_assoc()){
                $delete_imgs_slider[$i]['content'] = $row['content'];
                $delete_imgs_slider[$i]['id'] = $row['id'];
                $i++;
            }
        }

        /* ovdje kopa po folderu i ako ih nađe, obriše ih */
        foreach($delete_imgs_slider as $img){
            $img = $img['content'];
            $parts = explode('.', $img);

            if(file_exists(WEB_ROOT.'images/content/article-sliders/'.$id.'-'.$img)) unlink(WEB_ROOT.'images/content/article-sliders/'.$id.'-'.$img);

            if(file_exists(WEB_ROOT.'images/content/article-slider-thumbs/'.$id.'-'.$parts[0].'-thumbnail.'.$parts[1])) unlink(WEB_ROOT.'images/content/article-slider-thumbs/'.$id.'-'.$parts[0].'-thumbnail.'.$parts[1]);

            if(file_exists(WEB_ROOT.'images/content/article-slider-thumbs/'.$id.'-'.$parts[0].'-thumbnail-main.'.$parts[1])) unlink(WEB_ROOT.'images/content/article-slider-thumbs/'.$id.'-'.$parts[0].'-thumbnail-main.'.$parts[1]);
        }

        /* ovdje kopa nove fotografije koje su došle POSTOM i onda ih gura u foldere */
        imageUploadArticleSlider($id, $slider_img, 'article-sliders/', $main_img);

        /* update u bazu */
        $i = 0;

        foreach($slider_img['name'] as $key => $slider_img_name){

            $position = 0;
            $content = $slider_img_name;
            $main_content = 0;
            $article_id = $id;
            $content_type = 1;

            if($i == (int)$main_img){
                $main_content = 1;
            }

            if(($key+1) <= count($delete_imgs_slider)){
                $stmt = $conn->prepare("UPDATE article_content SET position = ?, content = ?, show_frontend = ? WHERE article_id = ? AND content_type = ? AND id = ?");
                $stmt->bind_param("isiiii", $position, $content, $main_content, $article_id, $content_type, $delete_imgs_slider[$i]['id']);
                $stmt->execute();
            } else {
                $stmt = $conn->prepare("INSERT INTO article_content (article_id, content_type, position, content, show_frontend) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("iiisi", $article_id, $content_type, $position, $content, $main_content);
                $stmt->execute();
            }
           
            $i++; 
        }
        
    }

    /* znači... okej, treba samo dohvatiti sve iz textarea i update-at šta god da je napravio
        ne treba slike ništa dirati, niti video, samo update na content_type = 1 article_id = ? i to je to I guess 
    */

    $article_paragraf_slidervideo = array_slice($arr_text_video, 2, -1, true);

    $slider_videos = [];
    $id = (int)mysqli_real_escape_string($conn, trim($id));
    $result = $conn->query("SELECT content, id FROM article_content WHERE article_id = $id AND content_type = 2");
    if($result->num_rows > 0){
        $i = 0;
        while($row = $result->fetch_assoc()){
            $slider_videos[$i]['id'] = $row['id'];
            $i++;
        }
    }

    $j = 0;
    foreach($article_paragraf_slidervideo as $key => $value){
        $pieces = explode("-", $key);
        if(strpos($pieces[0], 'paragraf') !== false){

            $paragraf = htmlentities($value,ENT_QUOTES,"UTF-8");

            $stmt = $conn->prepare("UPDATE article_content SET content = ? WHERE article_id = ? AND content_type = 3");
            $stmt->bind_param("si", $paragraf, $id);
            $stmt->execute();

        } elseif(strpos($pieces[0].'-'.$pieces[1], 'slider-video') !== false){

            if($pieces[2] <= count($slider_videos)){
                $stmt = $conn->prepare("UPDATE article_content SET content = ? WHERE article_id = ? AND id = ? AND content_type = 2");
                $stmt->bind_param("sii", $value, $id, $slider_videos[$j]['id']);
                $stmt->execute();
            } else {
                $stmt = $conn->prepare("INSERT INTO article_content (article_id, content_type, position, content, show_frontend) VALUES (?, 2, 0, ?, 0)");
                $stmt->bind_param("is",  $id, $value);
                $stmt->execute();
            }

        }
    }

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

    $title = $content['article_title'];
    $answer = $content['answer'];
    $header = $content['header'];
    $lang = 1;

    $paragraf = htmlentities($content['paragraf-1'],ENT_QUOTES,"UTF-8");

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

    $delete_imgs = [];
    $id = (int)mysqli_real_escape_string($conn, trim($id));
    $result = $conn->query("SELECT front_img FROM articles WHERE id = ".$conn->real_escape_string($id));
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $delete_imgs[] = $row['front_img'];
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

function selectArticleAndType($article_id, $article_category){
    global $conn;

    $data = [];
    $last_position = 1;

    $result = $conn->query("SELECT * FROM `articles` WHERE `id` = ".$conn->real_escape_string($article_id)." AND `category_id` = ".$conn->real_escape_string($article_category));
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $data = $row;
            $data['front_img'] = WWW_ROOT."images/content/article_uploads/".$row['front_img'];
        }
    } else return false;

    return $data;
}

function updateArticleWithCategory($article_id, $type, $title, $header, $paragraf, $front_image, $article_category_url = null){
    global $conn;
    
    $articleData = [];
    $result = $conn->query("SELECT * FROM articles WHERE id = ".$conn->real_escape_string($article_id)." ");
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $articleData = $row;
        }
    }

    if((int)$type === 2 && isset($front_image['name'])){
        if($front_image['name'][0] !== ''){
            // Obriši staru sliku
            if(file_exists(WEB_ROOT.'images/content/articles_upload/'.$article_id.'-'.$articleData['front_img'])) unlink(WEB_ROOT.'images/content/articles_upload/'.$article_id.'-'.$articleData['front_img']);
            
            // Postavi novu
            $original_image_path = WEB_ROOT."images/content/article_uploads/original/".$front_image['name'][0];            
            $file = $front_image['name'][0];
            $tmp_file = $front_image['tmp_name'][0];
            $img_name = pathinfo($file, PATHINFO_FILENAME);
            move_uploaded_file($tmp_file, $original_image_path);

            $frontImage = $article_id.'-'.$img_name.'.webp';
            $webp_image_path = WEB_ROOT."images/content/article_uploads/".$frontImage;

            resize_image($original_image_path, $webp_image_path, 600, 400, ZEBRA_IMAGE_BOXED);

            // baza            
            $stmt = $conn->prepare("UPDATE articles SET front_img = ? WHERE id = ? AND article_type = ?");
            $stmt->bind_param("sii", $frontImage,  $article_id, $type);
            $stmt->execute();
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
    if($article_category_url == null && empty($article_category_url)){

        $stmt = $conn->prepare("UPDATE `articles` SET `title` = ?, `header` = ?, `content` = ?, `article_link` = ? WHERE `id` = ? AND `article_type` = ?");
        $stmt->bind_param("ssssii", $title, $header, $paragraf, $url, $article_id, $type);

    } else {

        /* zbog mog glupog spajanja, mora prvo otici u article_categories da nade id koji zapravo veze sve. i fucked up hihi */
        $stmt = $conn->prepare("SELECT * FROM `article_categories` WHERE `category_name` = ?");
        $stmt->bind_param("s", $article_category_url);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->free_result();
        $row = $result->fetch_assoc();
        $article_category_id = $row['id'];
        $article_category_type = $row['category_type'];

        $stmt = $conn->prepare("UPDATE `articles` SET `title` = ?, `header` = ?, `content` = ?, `article_link` = ?, `category_id` = ? WHERE `id` = ? ");
        $stmt->bind_param("ssssii", $title, $header, $paragraf, $url, $article_category_id, $article_id);
    }
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


function selectMenuLinks(){
    global $conn;

    $query_categ = "SELECT 
    a.id, b.content, b.link, a.order, a.content AS `meni`, a.link AS `meni_link`, b.icon as icon, a.id AS `meni_id`, b.id AS `sub_id`, b.active 
    FROM `meni` AS a 
    LEFT JOIN `sub_meni` AS b ON b.meni_id = a.id
    ORDER BY a.order";
      $result_categ = $conn->query($query_categ);
      $result=[];
      $main_links = [];
      $sub_meni_link = [];
      $i = 0;
      while($row_categ = $result_categ->fetch_assoc() ){     
        if(!in_array($row_categ['meni'], $main_links)) {
          $main_links[$row_categ['meni_id']]['title'] = $row_categ['meni'];
          $main_links[$row_categ['meni_id']]['order'] = $row_categ['order'];
          $main_links[$row_categ['meni_id']]['id'] = $row_categ['id'];
          if(!empty($row_categ['meni_link']) || $row_categ['meni_link'] !='') {$main_links[$row_categ['meni_id']]['meni_link'] = $row_categ['meni_link'];}
        }
        if($row_categ['content'] != NULL){
        $sub_meni_link[$i]['link_name'] = $row_categ['content'];
        $sub_meni_link[$i]['link_url'] = $row_categ['link'];
        $sub_meni_link[$i]['link_id'] = $row_categ['sub_id'];
        $sub_meni_link[$i]['active'] = $row_categ['active'];
        $sub_meni_link[$i]['icon'] = $row_categ['icon'];
        }
        $i++;
      }
      return ['mainMenu'=>$main_links,'subMenu'=>$sub_meni_link];
}

function selectArticleCategoryName($category_id){
    global $conn;

    $stmt = $conn->prepare("SELECT `content` FROM `sub_meni` WHERE `category_id` = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    if($result->num_rows > 0){
        $stmt->free_result();
        $row = $result->fetch_assoc();
        $category_title = $row['content'];
        return $category_title;
    } else {
        $stmt = $conn->prepare("SELECT c.content 
        FROM `article_categories` AS a 
        LEFT JOIN `meni` AS c ON a.`category_name` = c.link 
        WHERE a.id = ?");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->free_result();
        if($result->num_rows > 0){
            $stmt->free_result();
            $row = $result->fetch_assoc();
            $category_title = $row['content'];
            return $category_title;
        } else return false;
    }
    
}

function selectMenuLinksForArticles(){
    global $conn;

    $query_categ = "SELECT 
    a.id, b.content, b.category_id, b.link, a.order, a.content AS `meni`, a.link AS `meni_link`, b.icon as icon, a.id AS `meni_id`, b.id AS `sub_id`, b.active 
    FROM `meni` AS a 
    LEFT JOIN `sub_meni` AS b ON b.meni_id = a.id 
    JOIN `article_categories` AS c ON c.category_name = b.link 
    WHERE c.category_type <> 4
    ORDER BY a.order";
      $result_categ = $conn->query($query_categ);
      $result=[];
      $main_links = [];
      $sub_meni_link = [];
      $i = 0;
      while($row_categ = $result_categ->fetch_assoc() ){     
        if(!in_array($row_categ['meni'], $main_links)) {
          $main_links[$row_categ['meni_id']]['title'] = $row_categ['meni'];
          $main_links[$row_categ['meni_id']]['order'] = $row_categ['order'];
          $main_links[$row_categ['meni_id']]['id'] = $row_categ['id'];
          if(!empty($row_categ['meni_link']) || $row_categ['meni_link'] !='') {$main_links[$row_categ['meni_id']]['meni_link'] = $row_categ['meni_link'];}
        }
        if($row_categ['content'] != NULL){
        $sub_meni_link[$i]['link_name'] = $row_categ['content'];
        $sub_meni_link[$i]['link_url'] = $row_categ['link'];
        $sub_meni_link[$i]['link_id'] = $row_categ['sub_id'];
        $sub_meni_link[$i]['active'] = $row_categ['active'];
        $sub_meni_link[$i]['icon'] = $row_categ['icon'];
        $sub_meni_link[$i]['category_id'] = $row_categ['category_id'];
        }
        $i++;
      }
      return ['mainMenu'=>$main_links,'subMenu'=>$sub_meni_link];
}

function selectMainMenu(){
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM `meni`");
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    $data = [];
    while($row = $result->fetch_assoc()){
        $data[] = $row;
    }
    return $data;
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

?>