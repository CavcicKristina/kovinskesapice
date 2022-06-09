-- MySQL dump 10.13  Distrib 5.6.23, for Win32 (x86)
--
-- Host: localhost    Database: kovinskesapicedb
-- ------------------------------------------------------
-- Server version	8.0.21

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `aboutus`
--

DROP TABLE IF EXISTS `aboutus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aboutus` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title1` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `content1` text COLLATE utf8mb4_general_ci NOT NULL,
  `img1` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `img2` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `title2` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `content2` text COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aboutus`
--

LOCK TABLES `aboutus` WRITE;
/*!40000 ALTER TABLE `aboutus` DISABLE KEYS */;
INSERT INTO `aboutus` VALUES (1,'&Scaron;to rade Kovinske &scaron;apice?','Sta Kovinske sapice rade? Radimo mnogo sta u skladu sa svojim mogucnostima. Spasavamo, lecimo i pokusavamo udomiti u domove gde ce imati ljubavi za njih. Sve je teze naci im dobre domove i zato je vecina ostala tu gde jeste.','MicrosoftTeams-image (8).png','MicrosoftTeams-image (10).png','Tko su Kovinske &Scaron;apice?','Kovinske sapice postoje od 1997 godine ( to je godina kada sam pocela da spasavam macice, kucice i bolesne zivotinje ) Tada je zapocela nasa misija za njih, onih koji nemaju prava glasa. Kako su godine prolazile tako je i broj zivotinja rasla. Domove smo im tesko nalazile jer je odgovorno vlasnistvo veliki pojam jos uvek, No ne odustajemo. Radimo na sterilizaciji najcesce su to macke. Hvatamo ih na human nacin i posle oporavka ih vracamo na staniste. Najvaznije nam je da se kastracija i sterilizacija sprovode da bi bilo sto manje napustenih na ulicama. Mi nemamo azil vec smo zivotinje smestili u nase domove. Nemamo privremene tete cuvalice, samo volju nas dve. Sve mace koje su u smestaju su nekada bili napusteni i zivot im je bio izlozen riziku da prezive. Pretezno su to mali macici, stenci ili bolesni. Zivotinje u svoje domove odlaze veterinarski zbrinute i socijalizovane. Pokusavamo nauciti ljude kako je zivot jedne zivotinje bitan kao i svaki drugi. Svi bi uzeli zdravu macu ili psa ali ne znaju koliko je svako od njih jedinstven. Narocito oni koji imahu neki hendikep. Nas rad se sprovodi u nasoj okolini. Svako moze postati jedan od nas! Mozes i ti! Kako pomoci? Donacijom hrane, lekova, prevoza i finansijski. Cak mozete biti od pomoci kao teta cuvalica i pomoc do pronalaska doma. Svaka sapica ima svoju pricu, budi i ti deo nje.');
/*!40000 ALTER TABLE `aboutus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article_imgs`
--

DROP TABLE IF EXISTS `article_imgs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `article_imgs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `article_id` int unsigned NOT NULL,
  `img` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `front` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_imgs`
--

LOCK TABLES `article_imgs` WRITE;
/*!40000 ALTER TABLE `article_imgs` DISABLE KEYS */;
INSERT INTO `article_imgs` VALUES (10,4,'4-MicrosoftTeams-image (2).webp',1),(11,4,'4-MicrosoftTeams-image (3).png',0),(12,4,'4-MicrosoftTeams-image (4).png',0),(13,5,'5-maca-hrana.jpg',0),(14,5,'5-pas-hrana.webp',1),(45,17,'17-pexels-lisa-fotios-1009922.webp',1),(46,17,'17-pexels-rabbit-mr-10633353.jpg',0),(47,17,'17-pexels-samson-katt-5255255.jpg',0),(48,18,'18-MicrosoftTeams-image (4).png',0),(49,18,'18-MicrosoftTeams-image (5).png',0),(50,18,'18-MicrosoftTeams-image (6).webp',1),(51,19,'19-MicrosoftTeams-image (9).webp',1),(52,19,'19-MicrosoftTeams-image (10).png',0),(53,19,'19-MicrosoftTeams-image (11).png',0);
/*!40000 ALTER TABLE `article_imgs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `articles`
--

DROP TABLE IF EXISTS `articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `articles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(40) COLLATE utf8mb4_general_ci NOT NULL,
  `active` int unsigned NOT NULL DEFAULT '0',
  `view_count` int unsigned NOT NULL DEFAULT '0',
  `deleted` int unsigned NOT NULL DEFAULT '0',
  `lang` int unsigned NOT NULL,
  `header` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `author` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `article_link` varchar(80) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `articles`
--

LOCK TABLES `articles` WRITE;
/*!40000 ALTER TABLE `articles` DISABLE KEYS */;
INSERT INTO `articles` VALUES (2,'testtest',0,0,1,1,'headeeeeer','Admin','2022-05-23 14:12:41','testtest','hello hello'),(3,'Naslovnica',1,0,1,1,'headeeeeer','Admin','2022-05-23 14:13:53','naslovnica','Lorem ipsum dolor sit amet'),(4,'title',0,43,0,1,'Header header',NULL,'2022-05-27 11:21:00','title','Be careful when updating records in a table! Notice the WHERE clause in the UPDATE statement. The WHERE clause specifies which record(s) that should be updated. If you omit the WHERE clause, all records in the table will be updated!'),(5,'wewewewew',0,52,0,1,'wewewe','Admin','2022-05-27 13:56:57','wewewewew','wewewewewewe'),(17,'Neki naslov da probamo',1,8,0,1,'Još malo testiram','Admin','2022-05-30 17:18:18','neki-naslov-da-probamo','This is the shorthand for flex-grow, flex-shrink and flex-basis combined. The second and third parameters (flex-shrink and flex-basis) are optional. The default is 0 1 auto, but if you set it with a single number value, like flex: 5;, that changes the flex-basis to 0%, so it&rsquo;s like setting flex-grow: 5; flex-shrink: 1; flex-basis: 0%;.'),(18,'Lorem ipsum dolor sit amet, co',1,8,0,1,'pit. Aliquam erat volutp','Admin','2022-05-30 17:30:44','lorem-ipsum-dolor-sit-amet-co',' Aliquam eu nisl vitae arcu egestas ultricies. Nam eget justo vel ipsum volutpat suscipit ac vitae felis. Vivamus vestibulum tincidunt enim, a aliquam est ornare a. Nulla facilisi. Sed id nibh aliquet tortor facilisis porttitor quis ut orci. Nulla eros felis, pulvinar nec rhoncus ut, sodales vitae elit. Aliquam pretium eu dui a semper. Phasellus ut dapibus velit. Praesent at ornare lacus. Curabitur auctor pulvinar dui, et congue nibh malesuada in. Maecenas condimentum pretium augue, sed molestie magna placerat vulputate. Etiam tincidunt sit amet libero non sollicitudin. In maximus sem ut sapien ornare, non pellentesque mauris molestie. Quisque mi purus, posuere quis felis nec, tincidunt maximus ligula. Integer faucibus turpis vel sodales dignissim. Aliquam vitae eleifend justo.\r\n\r\nVestibulum eu volutpat lectus. Vestibulum consequat fringilla ipsum sit amet suscipit. In ultricies justo vel diam porta, quis dapibus lectus volutpat. Morbi molestie fermentum tempus. Maecenas rutrum urna ut tellus porttitor tincidunt. Nullam ornare, tellus eget commodo maximus, massa dolor rutrum eros, non fringilla tortor augue sit amet augue. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec ut quam blandit urna placerat lobortis. Sed imperdiet diam et diam aliquet, in laoreet nulla porttitor. '),(19,' volutpat. Pellentesque eget just',1,43,0,1,'m augue, sed molestie magna placerat vulputate. E',NULL,'2022-05-30 17:31:29','volutpat-pellentesque-eget-just','Bla bla bla\r\nEnter\r\nBla bla bla');
/*!40000 ALTER TABLE `articles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cat_imgs`
--

DROP TABLE IF EXISTS `cat_imgs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_imgs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `cat_id` int unsigned NOT NULL,
  `img` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `front` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cat_imgs`
--

LOCK TABLES `cat_imgs` WRITE;
/*!40000 ALTER TABLE `cat_imgs` DISABLE KEYS */;
INSERT INTO `cat_imgs` VALUES (11,4,'4-maca-hrana.jpg',0),(12,4,'4-pexels-rabbit-mr-10633353.webp',1);
/*!40000 ALTER TABLE `cat_imgs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cats`
--

DROP TABLE IF EXISTS `cats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cats` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) COLLATE utf8mb4_general_ci NOT NULL,
  `active` int unsigned NOT NULL DEFAULT '0',
  `deleted` int unsigned NOT NULL DEFAULT '0',
  `spol` enum('musko','zensko') COLLATE utf8mb4_general_ci NOT NULL,
  `dob` enum('beba','mlado','odraslo','staro') COLLATE utf8mb4_general_ci NOT NULL,
  `pasmina` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `velicina` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `cijepljen` int unsigned NOT NULL DEFAULT '0',
  `cipiran` int unsigned NOT NULL DEFAULT '0',
  `kastriran` int unsigned NOT NULL DEFAULT '0',
  `slaganje` int unsigned NOT NULL DEFAULT '0',
  `socijaliziran` int unsigned NOT NULL DEFAULT '0',
  `plah` int unsigned NOT NULL DEFAULT '0',
  `aktivniji` int unsigned NOT NULL DEFAULT '0',
  `manje_aktivni` int unsigned NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL,
  `opis` text COLLATE utf8mb4_general_ci,
  `view_count` int unsigned NOT NULL,
  `animal_link` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cats`
--

LOCK TABLES `cats` WRITE;
/*!40000 ALTER TABLE `cats` DISABLE KEYS */;
INSERT INTO `cats` VALUES (4,'micika',1,0,'zensko','staro','','',1,0,0,1,1,1,0,1,'2022-06-01 21:07:17','wewewewewewewe',1,'micika1');
/*!40000 ALTER TABLE `cats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_settings`
--

DROP TABLE IF EXISTS `cms_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `default_lang` int unsigned NOT NULL DEFAULT '1',
  `default_charset` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_settings`
--

LOCK TABLES `cms_settings` WRITE;
/*!40000 ALTER TABLE `cms_settings` DISABLE KEYS */;
INSERT INTO `cms_settings` VALUES (1,1,'utf-8');
/*!40000 ALTER TABLE `cms_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_user_membership`
--

DROP TABLE IF EXISTS `cms_user_membership`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_user_membership` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_type` int unsigned NOT NULL,
  `title` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_user_membership`
--

LOCK TABLES `cms_user_membership` WRITE;
/*!40000 ALTER TABLE `cms_user_membership` DISABLE KEYS */;
INSERT INTO `cms_user_membership` VALUES (1,1,'admin'),(2,2,'moderator');
/*!40000 ALTER TABLE `cms_user_membership` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_users`
--

DROP TABLE IF EXISTS `cms_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `log_date` datetime NOT NULL,
  `user_ip` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `membership` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_users`
--

LOCK TABLES `cms_users` WRITE;
/*!40000 ALTER TABLE `cms_users` DISABLE KEYS */;
INSERT INTO `cms_users` VALUES (1,'admin','$2y$10$STk4ZBbVZ2a./JZpscyvX.o.90qoHLE5px7CZOwRgUaKnAUqIIoFW',NULL,'2022-06-09 12:26:07','::1',1),(2,'kovinske šapice','$2y$10$5pwYDi8SEsrI5XQg3ev8be9SGaQj8cBLuLPVQlnEbf0zHkaGIfe9C',NULL,'2022-05-23 11:37:32','::1',2);
/*!40000 ALTER TABLE `cms_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dog_imgs`
--

DROP TABLE IF EXISTS `dog_imgs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dog_imgs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `dog_id` int unsigned NOT NULL,
  `img` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `front` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dog_imgs`
--

LOCK TABLES `dog_imgs` WRITE;
/*!40000 ALTER TABLE `dog_imgs` DISABLE KEYS */;
INSERT INTO `dog_imgs` VALUES (4,1,'1-pexels-lisa-fotios-1009922.jpg',0),(5,1,'1-pexels-rabbit-mr-10633353.webp',1),(6,1,'1-pexels-samson-katt-5255255.jpg',0),(7,2,'2-pexels-lisa-fotios-1009922.jpg',0),(8,2,'2-pexels-rabbit-mr-10633353.jpg',0),(9,2,'2-pexels-samson-katt-5255255.webp',1),(11,4,'4-pexels-lisa-fotios-1009922.webp',1),(12,4,'4-pexels-rabbit-mr-10633353.jpg',0),(13,4,'4-pexels-samson-katt-5255255.jpg',0),(14,5,'5-MicrosoftTeams-image (7).webp',1);
/*!40000 ALTER TABLE `dog_imgs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dogs`
--

DROP TABLE IF EXISTS `dogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dogs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) COLLATE utf8mb4_general_ci NOT NULL,
  `active` int unsigned NOT NULL DEFAULT '0',
  `deleted` int unsigned NOT NULL DEFAULT '0',
  `spol` enum('musko','zensko') COLLATE utf8mb4_general_ci NOT NULL,
  `dob` enum('beba','mlado','odraslo','staro') COLLATE utf8mb4_general_ci NOT NULL,
  `pasmina` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `velicina` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `cijepljen` int unsigned NOT NULL DEFAULT '0',
  `cipiran` int unsigned NOT NULL DEFAULT '0',
  `kastriran` int unsigned NOT NULL DEFAULT '0',
  `slaganje` int unsigned NOT NULL DEFAULT '0',
  `socijaliziran` int unsigned NOT NULL DEFAULT '0',
  `plah` int unsigned NOT NULL DEFAULT '0',
  `aktivniji` int unsigned NOT NULL DEFAULT '0',
  `manje_aktivni` int unsigned NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL,
  `opis` text COLLATE utf8mb4_general_ci,
  `view_count` int unsigned NOT NULL,
  `animal_link` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dogs`
--

LOCK TABLES `dogs` WRITE;
/*!40000 ALTER TABLE `dogs` DISABLE KEYS */;
INSERT INTO `dogs` VALUES (1,'pas1',0,0,'zensko','odraslo','peso','srednja',1,0,0,1,0,0,1,0,'2022-05-31 16:04:26','novo novo',0,'pas11'),(2,'pas 2',1,0,'musko','mlado','Njemački ovčar','Veliki',0,1,1,1,0,0,0,1,'2022-05-31 16:05:17','ovo je jedna jaaako velika maza',21,'pas-2'),(3,'qweqweqweqweqweqwe',0,1,'zensko','beba','','',1,1,0,0,0,0,1,0,'2022-05-31 21:31:35','qweqweqweqweqw',0,'qweqweqweqweqweqwe'),(4,'pas 3',1,0,'musko','mlado','','',1,0,0,1,1,0,1,0,'2022-06-01 21:17:00','qweqweqweqweqweqweqweqw',46,'pas-31'),(5,'Poku&scaron;aj neki',1,0,'musko','mlado','Mje&scaron;anac','Srednja',1,0,1,0,0,1,1,0,'2022-06-02 16:46:59','Hello hello',7,'pokuscaronaj-neki');
/*!40000 ALTER TABLE `dogs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `donations`
--

DROP TABLE IF EXISTS `donations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `donations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('1','2','3','4') COLLATE utf8mb4_general_ci NOT NULL,
  `content` text COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `donations`
--

LOCK TABLES `donations` WRITE;
/*!40000 ALTER TABLE `donations` DISABLE KEYS */;
INSERT INTO `donations` VALUES (1,'1','Donacije'),(2,'2','Budi deo nas na nasoj aukci &quot; Humanitarna aukcija za Kovinske sapice &quot; Doniraj hranu Uvek nam tebaju granule za sterilisane mace Tomi konzerve Medicinska hrana koju mi tesko kupujemo Mokra i suva hrana za mace koje se hrane na hranilistima. Za pse kuvamo svaki dan Najcesce su nam potrebne tablete ili ampule za parazite. Posip za mace i ono najvaznije hrana.'),(8,'3','maca-hrana.jpg'),(9,'3','pas-hrana.jpg'),(10,'4','MicrosoftTeams-image.png');
/*!40000 ALTER TABLE `donations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lang`
--

DROP TABLE IF EXISTS `lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lang` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `abbr` varchar(5) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lang`
--

LOCK TABLES `lang` WRITE;
/*!40000 ALTER TABLE `lang` DISABLE KEYS */;
INSERT INTO `lang` VALUES (1,'Hrvatski','hr');
/*!40000 ALTER TABLE `lang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `pretty_url` varchar(40) COLLATE utf8mb4_general_ci NOT NULL,
  `template` varchar(40) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES (1,'','naslovnica'),(3,'o-nama','o_nama'),(4,'donacije','donacije'),(5,'kontakt','kontakt'),(6,'novosti','novosti'),(7,'udomi-psa','animal_gallery'),(8,'udomi-macku','animal_gallery');
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-06-09 12:26:45
