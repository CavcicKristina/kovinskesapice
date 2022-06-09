<?php 

class Connection {

    private static $instance = null;
    private $conn;

    private function __construct()
    {
        $this->conn = mysqli_connect(SQLSERVER, SQLUSER, SQLPASS, SQLDB) or die("Database conn not established.");
		$this->conn->set_charset("utf8mb4");
		$this->conn->query("SET collation_connection = utf8mb4_general_ci");
    }

    public static function link() {
        if (self::$instance === null) {
            self::$instance = new Connection();
        }
        return self::$instance; 
    }

    public function getConnection(){
        return $this->conn;
    }

    /* Spriječava dupliciranje konekcije */
    private function __clone(){}
}

function define_safe($name, $val){
	if(!defined($name)) define("$name", $val);
}

function getWebSettings(){
	global $conn;
	$sql = "SELECT
			`default_lang` AS 'H_LANG',
			`default_page` AS 'H_HOMEPAGE',
			`default_charset` AS 'H_CHARSET',
			`social_token` AS 'H_SOCIAL'	
			FROM `settings` WHERE `id`='1' LIMIT 1";
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

function getLangs(){
	global $conn;
	$langs=[];
	$sql = "SELECT * FROM `lang`";
	$result = $conn->query($sql);		
	if($result->num_rows > 0){
		while ($row = $result->fetch_assoc()) {
	 		foreach($row as $key => $value){
				$langs[$row['id']][$key]=$value;
			}
		}
		return $langs;
	}
	return false;
}

function getLangNameFromId($var){
	global $conn;
	$sql = "SELECT * FROM `lang` WHERE id='".$conn->real_escape_string($var)."' LIMIT 1";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();

	if(isset($row['abbr']) && !empty($row['abbr'])) $out = $row['abbr'];
	else $out = '';

	return $out;
}

function getLangIDFromName($var){
	global $conn;
	$sql = "SELECT * FROM `lang` WHERE abbr='".$conn->real_escape_string($var)."' LIMIT 1";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();

	if(isset($row['id']) && !empty($row['id'])) $out = $row['id'];
	else $out = '';

	return $out;
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

function blogsPagination($page_number){
	global $conn;

	if(!ctype_digit((string)$page_number)) $page_number = 1;

	$results_per_page = 8;

	/* nalazi sve blogove sa odredenim jezikom i ako su aktivni */
	$stmt = $conn->prepare("SELECT * FROM `articles` WHERE `active` = 1 AND `deleted` = 0");
	if($stmt->execute()){ 
		$result = $stmt->get_result();
		$stmt->free_result();
		$number_of_result = $result->num_rows;
	} else return false;

	/* odreduje koliko stranica ce se stvoriti za sve nadene blogove */
	$number_of_pages = ceil($number_of_result / $results_per_page);

	/* ako nema broja stranice, postavlja na jedan, u suprotnom postavlja broj stranice koji postoji */
	if(!isset($page_number) || empty($page_number)) $page = 1;
	else $page = $page_number;

	/* ako broj koji je u url-u prelazi broj stranica koliko ih ima, stavit ce na prvu stranicu blogova */
	if($page > $number_of_pages) $page = 1;

	/* odreduje mjeru koliko ce biti na trenutnoj stranici blogova (1 1-12, 2 13-25, 3 26-38...) */
	$page_first_result = ($page - 1) * $results_per_page;

	/* dohvaca sve clanke koji trebaju biti na toj stranici */
	$content = [];
	$stmt = $conn->prepare("SELECT * FROM `articles` WHERE `active` = 1 AND `deleted` = 0 ORDER BY date_created DESC LIMIT ".$page_first_result.",".$results_per_page);
	if($stmt->execute()){ 
		$result = $stmt->get_result();
		$stmt->free_result();
		$i = 0;
		while($row = $result->fetch_assoc()){
			$content[$i] = $row;
			$date_for_output = date_create($row['date_created']);
			$content[$i]['date_created'] = date_format($date_for_output, "d.m.Y.");
			$i++;
		}
	} else return false;

    foreach($content as $key => $cont){
        $stmt = $conn->prepare("SELECT * FROM `article_imgs` WHERE `front` = 1 AND `article_id` = ?");
        $stmt->bind_param("i", $cont['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->free_result();
        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            $content[$key]['front_img'] = WWW_ROOT.'images/articles/'.$row['img'];
        } else {
            $content[$key]['front_img'] = WWW_ROOT.'images/articles/MicrosoftTeams-image (2).png';
        }
    }

	/* 
	treba uzeti trenutnu stranicu i po njoj odrediti gdje treba ici
	Ako se  nalazi na prvoj stranici, strelica lijevo ne radi, a desno pokazuje 2,3,>
	Ako se nalazi na zadnjoj stranici, pokazuje zadnji broj i lijevo pokazuje n-1,n-2,<
	Ako je broj koji je veći od 1 i manji od n, onda pokazuje taj broj u sredini kao aktivan i brojevi n-1,n+1,<,>
	*/

	/* postavljanje strelica i stranica */
	$pages_links = [];
	$i = 0;
	if((int)$page === 1){

		$strelice['strelica-lijevo'] = 'disabled';
		$strelice['strelica-lijevo-link'] = '';
		if((int)$page == (int)$number_of_pages){
			$strelice['strelica-desno'] = 'disabled';
			$strelice['strelica-desno-link'] = '';
		} else {
			$strelice['strelica-desno'] = '';
			$strelice['strelica-desno-link'] = $page + 1;
			$strelice['strelica-desno-link'] = '/novosti/'.$strelice['strelica-desno-link'];
		}
		for($j = $page; $j <= $number_of_pages; $j++){
			if($i < 3){
				if($i == 0) $pages_links[$i]['class'] = 'active';
				else $pages_links[$i]['class'] = '';
				$pages_links[$i]['link'] = '/novosti/'.$j;
				$pages_links[$i]['number'] = $j;
				
			} 
			$i++;
		}

	} else if((int)$page === (int)$number_of_pages){

		$strelice['strelica-desno'] = 'disabled';
		$strelice['strelica-desno-link'] = '';
		$strelice['strelica-lijevo'] = '';
		$strelice['strelica-lijevo-link'] = $page - 1;
		if($page == 2) $page -= 1;
		else $page -= 2;
		/* for($page = $page_number; $page <= $number_of_pages; $page++){ */
		for($j = $page; $j <= $number_of_pages; $j++){
			if($i < 3){
				if($i == 2) $pages_links[$i]['class'] = 'active';
				else $pages_links[$i]['class'] = '';
				$pages_links[$i]['link'] = $j;
				$pages_links[$i]['number'] = $j;
				
			} 
			$i++;
		}

	} else{

		$strelice['strelica-lijevo'] = '';
		$strelice['strelica-lijevo-link'] = $page - 1;
		$strelice['strelica-desno'] = '';
		$strelice['strelica-desno-link'] = $page + 1;
		$page -= 1;
		/* for($page = $page_number; $page <= $number_of_pages; $page++){ */
		for($j = $page; $j <= $number_of_pages; $j++){
			if($i < 3){
				if($i == 1) $pages_links[$i]['class'] = 'active';
				else $pages_links[$i]['class'] = '';
				$pages_links[$i]['link'] = $j;
				$pages_links[$i]['number'] = $j;
				
			} 
			$i++;
		}

	}

	return [$content,$pages_links,$strelice];
}

function selectArticle($par2){
	global $conn;

	$sql = "SELECT `title`, `author`, `date_created`, `id`, `content` FROM `articles` 
	WHERE `article_link`=? AND `active`='1' AND `deleted`='0' LIMIT 1";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('s', $par2);
	if($stmt->execute()){
		$result = $stmt->get_result();
		$stmt->free_result();
		if($result->num_rows > 0){
			if($row = $result->fetch_assoc()){
				return $row;
			}
		} else return false;
	} else return false;
}

function selectAnimal($par1,$par2){
	global $conn;

	if($par1 == 'udomi-psa'){
		$main_table = 'dogs';
		$imgs_table = 'dog_imgs';
		$imgs_id = 'dog_id';
	} elseif ($par1 == 'udomi-macku'){
		$main_table = 'cats';
		$imgs_table = 'cat_imgs';
		$imgs_id = 'cat_id';
	}

	$sql = "SELECT `id`, `animal_link` FROM `".$main_table."` 
	WHERE `animal_link`=? AND `active`='1' AND `deleted`='0' LIMIT 1";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('s', $par2);
	if($stmt->execute()){
		$result = $stmt->get_result();
		$stmt->free_result();
		if($result->num_rows > 0){
			if($row = $result->fetch_assoc()){
				return $row;
			}
		} else return false;
	} else return false;
}

function selectFullArticle($id){
	global $conn;

	$sql="SELECT * FROM `articles` WHERE `id`=? LIMIT 1";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('i', $id);
	if($stmt->execute()){
		$result = $stmt->get_result();
		$stmt->free_result();
		if($result->num_rows > 0){
			$row = $result->fetch_assoc();
			$date_for_output = date_create($row['date_created']);
			$row['date_created'] = date_format($date_for_output, "d.m.Y.");

			$sql="SELECT * FROM `article_imgs` WHERE `article_id`= ?";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param('i', $id);
			$stmt->execute();
			$result = $stmt->get_result();
			$stmt->free_result();
			$i = 0;
			while($row2 = $result->fetch_assoc()){
				if($row2['front'] == 0) $row2['img'] = WWW_ROOT.'images/articles/original/'.$row2['img'];
				else $row2['img'] = WWW_ROOT.'images/articles/'.$row2['img'];
				$row['imgs'][$i] = $row2;
				$i++;
			}

			$sql="UPDATE `articles` SET `view_count` = `view_count` + 1 WHERE `id` = ?";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param('i', $id);
			$stmt->execute();

			$row['content'] = nl2br($row['content']);

			return $row;
			
		}
		
		return false;
	}
	return false;
}

function selectFullAnimalInfo($id, $parentPage){
	global $conn;

	if($parentPage == 'udomi-psa'){
		$main_table = 'dogs';
		$imgs_table = 'dog_imgs';
		$imgs_id = 'dog_id';
	} elseif ($parentPage == 'udomi-macku'){
		$main_table = 'cats';
		$imgs_table = 'cat_imgs';
		$imgs_id = 'cat_id';
	}

	$sql="SELECT * FROM `".$main_table."` WHERE `id`=? LIMIT 1";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('i', $id);
	if($stmt->execute()){
		$result = $stmt->get_result();
		$stmt->free_result();
		if($result->num_rows > 0){
			$row = $result->fetch_assoc();
			$date_for_output = date_create($row['date_created']);
			$row['date_created'] = date_format($date_for_output, "d.m.Y.");
			$row['name'] = html_entity_decode($row['name'], ENT_QUOTES,"UTF-8");
			$row['pasmina'] = html_entity_decode($row['pasmina'], ENT_QUOTES,"UTF-8");
			$row['velicina'] = html_entity_decode($row['velicina'], ENT_QUOTES,"UTF-8");

			$sql="SELECT * FROM `".$imgs_table."` WHERE `".$imgs_id."`= ?";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param('i', $id);
			$stmt->execute();
			$result = $stmt->get_result();
			$stmt->free_result();
			$i = 0;
			while($row2 = $result->fetch_assoc()){
				if($row2['front'] == 0) $row2['img'] = $row2['img'];
				else $row2['img'] = WWW_ROOT.'images/animals/'.$row2['img'];
				$row['imgs'][$i] = $row2;
				$i++;
			}

			$sql="UPDATE `".$main_table."` SET `view_count` = `view_count` + 1 WHERE `id` = ?";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param('i', $id);
			$stmt->execute();

			foreach($row as $key => $data){
				if(strcmp($key,'id') != 0 && strcmp($key,'name') != 0 && strcmp($key,'active') != 0 && strcmp($key,'deleted') != 0 && strcmp($key,'date_created') != 0 && strcmp($key,'opis') != 0 && strcmp($key,'view_count') != 0 && strcmp($key,'animal_link') != 0 && strcmp($key,'imgs') != 0 ) $filter[$key] = $data;
			}

			$row['filter'] = $filter;

			$row['opis'] = nl2br($row['opis']);

			return $row;
			
		}
		
		return false;
	}
	return false;
}

function selectArticleLinks($current_article_id){
	global $conn;

	$content=[];

	/* dohvaćanje prvog članka iza trenutnog članka, ako ne postoji onda false */
	$sql="SELECT * FROM `articles` WHERE `id` = (SELECT MAX(`id`) FROM `articles` WHERE `id` < ?) AND `active` = 1 AND `deleted` = 0";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('i', $current_article_id);
	if($stmt->execute()){
		$result = $stmt->get_result();
		if($result->num_rows > 0){

			while($row = $result->fetch_assoc()){
				$content['previous1'] = $row;
			}
			$sql="SELECT * FROM `article_imgs` WHERE `article_id` = ? AND `front` = 1";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param('i', $content['previous1']['id']);
			$stmt->execute();
			$result = $stmt->get_result();
			while($row = $result->fetch_assoc()) $content['previous1']['front_img'] = WWW_ROOT.'images/articles/'.$row['img'];

			/* dohvaćanje drugog članka, ako ne postoji, onda vraća samo prvi */
			$sql="SELECT * FROM `articles` WHERE `id` = (SELECT MAX(`id`) FROM `articles` WHERE `id` < ?) AND `active` = 1 AND `deleted` = 0";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param('i', $content['previous1']['id']);
			$stmt->execute();
			$result = $stmt->get_result();
			if($result->num_rows > 0){

				while($row = $result->fetch_assoc()){
					$content['previous2'] = $row;
				}
				$sql="SELECT * FROM `article_imgs` WHERE `article_id` = ? AND `front` = 1";
				$stmt = $conn->prepare($sql);
				$stmt->bind_param('i', $content['previous2']['id']);
				$stmt->execute();
				$result = $stmt->get_result();
				while($row = $result->fetch_assoc()) $content['previous2']['front_img'] = WWW_ROOT.'images/articles/'.$row['img'];

				return $content;

			} else return $content;


		} else return false;
		
	} else return false;
}

function selectLastFourArticles(){
	global $conn;

	$stmt = $conn->prepare("SELECT * FROM `articles` WHERE `active` = 1 AND `deleted` = 0 ORDER BY `id` DESC LIMIT 4");
	$stmt->execute();
	$result = $stmt->get_result();
	$stmt->free_result();
	if($result->num_rows > 0){
		$articles = [];
		while($row = $result->fetch_assoc()) $articles[] = $row;

		$stmt = $conn->prepare("SELECT * FROM `article_imgs` WHERE `article_id` = ? AND `front` = 1");
		$i = 0;
		foreach($articles as $article){
			$stmt->bind_param("i", $article['id']);
			$stmt->execute();
			$result = $stmt->get_result();
			$stmt->free_result();
			if($result->num_rows > 0){
				$row = $result->fetch_assoc();
				$articles[$i]['front_img'] = WWW_ROOT.'images/articles/'.$row['img'];
			} else {
				$articles[$i]['front_img'] = WWW_ROOT.'images/articles/MicrosoftTeams-image (2).png';
			}
			$i++;
		}

		return $articles;

	} else return false;
}

function selectAllAnimals($currentPage){
	global $conn;

	if($currentPage == 'udomi-psa'){
		$main_table = 'dogs';
		$imgs_table = 'dog_imgs';
		$imgs_id = 'dog_id';
	} elseif ($currentPage == 'udomi-macku'){
		$main_table = 'cats';
		$imgs_table = 'cat_imgs';
		$imgs_id = 'cat_id';
	}

	$stmt = $conn->prepare("SELECT * FROM `".$main_table."` WHERE `active` = 1 AND `deleted` = 0 ORDER BY `id` DESC");
	$stmt->execute();
	$result = $stmt->get_result();
	$stmt->free_result();
	if($result->num_rows > 0){
		$articles = [];
		while($row = $result->fetch_assoc()) $articles[] = $row;

		$stmt = $conn->prepare("SELECT * FROM `".$imgs_table."` WHERE `".$imgs_id."` = ? AND `front` = 1");
		$i = 0;
		foreach($articles as $article){
			$stmt->bind_param("i", $article['id']);
			$stmt->execute();
			$result = $stmt->get_result();
			$stmt->free_result();
			if($result->num_rows > 0){
				$row = $result->fetch_assoc();
				$articles[$i]['front_img'] = WWW_ROOT.'images/animals/'.$row['img'];
			} else {
				$articles[$i]['front_img'] = WWW_ROOT.'images/articles/MicrosoftTeams-image (2).png';
			}
			$i++;
		}

		return $articles;

	} else return false;
}

function selectAllAnimalsNaslovnica(){
	global $conn;

	$animals = [];

	for($j = 0; $j < 2; $j++){
		if($j == 0){
			$main_table = 'dogs';
			$imgs_table = 'dog_imgs';
			$imgs_id = 'dog_id';
		} elseif ($j == 1){
			$main_table = 'cats';
			$imgs_table = 'cat_imgs';
			$imgs_id = 'cat_id';
		}
	
		$stmt = $conn->prepare("SELECT * FROM `".$main_table."` WHERE `active` = 1 AND `deleted` = 0 ORDER BY `id` DESC LIMIT 4");
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->free_result();
		if($result->num_rows > 0){
			while($row = $result->fetch_assoc()) $animals[] = $row;
	
			$stmt = $conn->prepare("SELECT * FROM `".$imgs_table."` WHERE `".$imgs_id."` = ? AND `front` = 1");
			$i = 0;
			foreach($animals as $article){
				$stmt->bind_param("i", $article['id']);
				$stmt->execute();
				$result = $stmt->get_result();
				$stmt->free_result();
				if($result->num_rows > 0){
					$row = $result->fetch_assoc();
					$animals[$i]['front_img'] = WWW_ROOT.'images/animals/'.$row['img'];
				} else {
					$animals[$i]['front_img'] = WWW_ROOT.'images/articles/MicrosoftTeams-image (2).png';
				}
				$i++;
			}
	
		} else continue;
	}

	return $animals;
	
}

function filterAnimals($currentPage, $filter_options){
	global $conn;

	if($currentPage == 'udomi-psa'){
		$main_table = 'dogs';
		$imgs_table = 'dog_imgs';
		$imgs_id = 'dog_id';
	} elseif ($currentPage == 'udomi-macku'){
		$main_table = 'cats';
		$imgs_table = 'cat_imgs';
		$imgs_id = 'cat_id';
	}

	$where = "";
	$param = "";

	/*  */
	if(isset($filter_options['spol'])){ 
		foreach($filter_options['spol'] as $key => $spol){
			$where .= "`spol` = '".$spol."'";
			$param .= "s";
			if(array_key_last($filter_options['spol']) != $key) $where .= " OR ";
		}
		$where .= " AND ";
	}
    if(isset($filter_options['dob'])){
		foreach($filter_options['dob'] as $key => $dob){
			$where .= "`dob` = '".$dob."'";
			$param .= "s";
			if(array_key_last($filter_options['dob']) != $key) $where .= " OR ";
		}
		$where .= " AND ";
	}
    if(isset($filter_options['cijepljen'])){ 
		$where .= "`cijepljen` = 1 AND ";
		$param .= "i";
	}
    if(isset($filter_options['cipiran'])){
		$where .= "`cipiran` = 1 AND ";
		$param .= "i";
	}
    if(isset($filter_options['kastriran'])){
		$where .= "`kastriran` = 1 AND ";
		$param .= "i";
	}
    if(isset($filter_options['slaganje'])){
		$where .= "`slaganje` = 1 AND ";
		$param .= "i";
	}
    if(isset($filter_options['socijaliziran'])){
		$where .= "`socijaliziran` = 1 AND ";
		$param .= "i";
	}
    if(isset($filter_options['plah'])){
		$where .= "`plah` = 1 AND ";
		$param .= "i";
	}
    if(isset($filter_options['aktivniji'])){
		$where .= "`aktivniji` = 1 AND ";
		$param .= "i";
	}
    if(isset($filter_options['manje-aktivni'])){
		$where .= "`manje_aktivni` = 1 AND ";
		$param .= "i";
	}
	
	$stmt = $conn->prepare("SELECT * FROM `".$main_table."` WHERE ".$where."`active` = 1 AND `deleted` = 0 ORDER BY `id` DESC");
	$stmt->execute();
	$result = $stmt->get_result();
	$stmt->free_result();
	if($result->num_rows > 0){
		$articles = [];
		while($row = $result->fetch_assoc()) $articles[] = $row;

		$stmt = $conn->prepare("SELECT * FROM `".$imgs_table."` WHERE `".$imgs_id."` = ? AND `front` = 1");
		$i = 0;
		foreach($articles as $article){
			$stmt->bind_param("i", $article['id']);
			$stmt->execute();
			$result = $stmt->get_result();
			$stmt->free_result();
			if($result->num_rows > 0){
				$row = $result->fetch_assoc();
				$articles[$i]['front_img'] = WWW_ROOT.'images/animals/'.$row['img'];
			} else {
				$articles[$i]['front_img'] = WWW_ROOT.'images/articles/MicrosoftTeams-image (2).png';
			}
			$i++;
		}

		return $articles;

	} else return false;
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
    $content['content1'] = nl2br(html_entity_decode($content['content1'], ENT_QUOTES, "UTF-8"));
    $content['content2'] = nl2br(html_entity_decode($content['content2'], ENT_QUOTES, "UTF-8"));
    if($content['img1'] != '0') $content['img1'] = WWW_ROOT."images/aboutus/".$content['img1'];
    if($content['img2'] != '0') $content['img2'] = WWW_ROOT."images/aboutus/".$content['img2'];

    return $content;
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
        if($row['type'] == 2) $content['sadrzaj'] = nl2br(html_entity_decode($row['content'], ENT_QUOTES, "UTF-8"));
        if($row['type'] == 3) $content['imgs'][] = WWW_ROOT."images/donacije/predmeti/".$row['content'];
        if($row['type'] == 4) $content['racun'] = WWW_ROOT."images/donacije/racun/".$row['content'];
    }

    return $content;

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
        if($row['type'] == 2) $content['lokacije'] = nl2br(html_entity_decode($row['content'], ENT_QUOTES, "UTF-8"));
        if($row['type'] == 3) $content['telefon'] = nl2br(html_entity_decode($row['content'], ENT_QUOTES, "UTF-8"));
        if($row['type'] == 4) $content['email'] =nl2br( html_entity_decode($row['content'], ENT_QUOTES, "UTF-8"));
        if($row['type'] == 5) $content['email_form'] = html_entity_decode($row['content'], ENT_QUOTES, "UTF-8");
        if($row['type'] == 6) $content['pass_form'] = html_entity_decode($row['content'], ENT_QUOTES, "UTF-8");
    }

    return $content;

}

function selectRotator($lang="1"){
	global $conn;

	$stmt = $conn->prepare("SELECT 
								`a`.`title`,
								`a`.`content`,
								`a`.`button`,
								`b`.`img`
							FROM
								`glavni_rotator` as a
								LEFT OUTER JOIN `glavni_rotator_img` as b ON (`a`.`slide_id` = `b`.`slide_id`)
							WHERE								 
								`a`.`lang` = ? ");
    if(!$stmt->bind_param("i", $lang)) $stmt->error;
    $stmt->execute();
	$result = $stmt->get_result();
	$i = 0;
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()){
			$rotator_content[$i]['title'] = $row['title'];
			$rotator_content[$i]['content'] = html_entity_decode($row['content'], ENT_QUOTES);
			$rotator_content[$i]['button'] = $row['button'];
			$rotator_content[$i]['img'] = $row['img'];
			$i++;
		}
	} else return false;	
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
        $content['content'] = nl2br(html_entity_decode($row['content'], ENT_QUOTES, "UTF-8"));
    }

    return $content;
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
        $content['content'] = nl2br(html_entity_decode($row['content'], ENT_QUOTES, "UTF-8"));
        $content['link'] = urldecode($row['link']);
        $content['img'] = WWW_ROOT.'images/'.$row['img'];
    }

    return $content;
}

?>