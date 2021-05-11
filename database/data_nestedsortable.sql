/*
 Navicat MySQL Data Transfer

 Source Server         : KOHALIK
 Source Server Type    : MySQL
 Source Server Version : 80019
 Source Host           : localhost:3306
 Source Schema         : qcubed-4

 Target Server Type    : MySQL
 Target Server Version : 80019
 File Encoding         : 65001

 Date: 11/05/2021 16:59:46
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for article
-- ----------------------------
DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `menu_content_id` int unsigned DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `category_id` int unsigned DEFAULT NULL,
  `title_slug` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `picture` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `picture_description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `author_source` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `assigned_by_user` int unsigned DEFAULT NULL,
  `author` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `confirmation_asking` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `menu_content_id_idx` (`menu_content_id`) USING BTREE,
  KEY `category_id_idx` (`category_id`) USING BTREE,
  KEY `user_id_idx` (`assigned_by_user`) USING BTREE,
  CONSTRAINT `category_id_article_fk` FOREIGN KEY (`category_id`) REFERENCES `category_of_article` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `menu_content_id_article_fk` FOREIGN KEY (`menu_content_id`) REFERENCES `menu_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_id_article_fk` FOREIGN KEY (`assigned_by_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of article
-- ----------------------------
BEGIN;
INSERT INTO `article` VALUES (3, 27, 'More about the organisation', 6, 'more-about-the-organisation', NULL, NULL, NULL, '<p>Tere, head s&otilde;brad &uuml;le Eesti!</p>\n\n<p>Meie loodame, et teie k&otilde;ik p&uuml;site kodus selle n&auml;dalavahetuse ja esimesed 2 n&auml;dalat, nii et saame koroonaviiruse pandeemiast ruttu lahti. See on meie k&otilde;igi enda teha.</p>\n\n<p>Meieni on j&otilde;udnud paljude kurtide inimeste suur mure, et mis saab puude pikendamisest, just nendel inimestel, kellel l&otilde;peb puue m&auml;rtsis-aprillis-mais. Seoses koroonaviiruse pandeemiaga pikendas Sotsiaalkindlustusamet puude kehtivust 6 kuuni. Nii et praegu pole vaja muretseda.</p>\n\n<p>Kui kriisiaeg l&otilde;peb, siis tuleb ikkagi esimesel v&otilde;imalusel puuet pikendada. Aga t&ouml;&ouml;v&otilde;ime hindamist tuleb ikkagi teha, loomulikult kaugt&otilde;lke kaudu.</p>\n\n<p><span class=\"marker\">Kuidas meie saame sellises olukorras teha?</span></p>\n\n<p>Siin tuleb teha selgeks erinevused SKA Skype kaugt&otilde;lketeenuse ja Viipekeelet&otilde;lkide Osa&uuml;hing Skype kaugt&otilde;lketeenuse vahel. SKA kaugt&otilde;lketeenus on sissehelistamisega &uuml;le Eesti, Viipekeelet&otilde;lkide Osa&uuml;hingu teenus on ettetellitav millise situatsiooni kirjeldamise ja aja broneerimisega. SKA ei tasu sellise teenuse eest. Teenuse tasustamine toimub samadel alustel nagu tavat&otilde;lketeenuse tasustamine.</p>\n\n<p>Palun tutvuge antud linkidega!</p>\n', '2020-08-13 16:53:02', '2021-02-18 23:45:18', 9, 'John Doe', 0);
INSERT INTO `article` VALUES (14, 33, 'Juhatusest lähemalt', NULL, 'juhatusest-lahemalt', NULL, NULL, NULL, '', '2020-08-23 12:59:39', '2021-01-08 22:25:34', 10, 'Alex Smith', 1);
INSERT INTO `article` VALUES (21, 32, 'Kontaktist saab rohkem teada', NULL, 'kontaktist-saab-rohkem-teada', NULL, NULL, NULL, NULL, '2020-09-04 14:29:45', '2020-09-27 17:59:34', 12, 'Brett Carlisle', 1);
INSERT INTO `article` VALUES (31, 250, 'Tallinna teenused', 8, 'tallinna-teenused', NULL, NULL, NULL, '<p>fghjkl&ouml; fghjk 7777</p>\n\n<p>&nbsp;</p>\n', '2020-09-06 07:59:22', '2020-11-27 20:57:48', 11, 'Samantha Jones', 0);
INSERT INTO `article` VALUES (32, 252, 'Pärnu teenused', NULL, 'parnu-teenused', NULL, NULL, NULL, '<p>ertyuio dfghjkl</p>\n', '2020-09-06 08:03:55', '2020-11-27 20:55:33', 9, 'John Doe', 0);
INSERT INTO `article` VALUES (33, 248, 'Projektide arhiiv', NULL, 'projektide-arhiiv', NULL, '', NULL, '', '2020-09-06 08:06:13', '2020-11-27 01:46:39', 10, 'Alex Smith', 0);
INSERT INTO `article` VALUES (41, 263, 'Paide teenused', 8, 'paide-teenused', NULL, '', '', '', '2020-09-08 04:38:15', '2020-11-27 23:51:49', 12, 'Brett Carlisle', 0);
COMMIT;

-- ----------------------------
-- Table structure for articles_editors_assn
-- ----------------------------
DROP TABLE IF EXISTS `articles_editors_assn`;
CREATE TABLE `articles_editors_assn` (
  `articles_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  PRIMARY KEY (`articles_id`,`user_id`),
  KEY `articles_id_idx` (`articles_id`) USING BTREE,
  KEY `articles_users_assn_2` (`user_id`),
  CONSTRAINT `articles_users_assn_1` FOREIGN KEY (`articles_id`) REFERENCES `article` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `articles_users_assn_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of articles_editors_assn
-- ----------------------------
BEGIN;
INSERT INTO `articles_editors_assn` VALUES (3, 12);
INSERT INTO `articles_editors_assn` VALUES (14, 9);
INSERT INTO `articles_editors_assn` VALUES (14, 12);
COMMIT;

-- ----------------------------
-- Table structure for category_of_article
-- ----------------------------
DROP TABLE IF EXISTS `category_of_article`;
CREATE TABLE `category_of_article` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_enabled` tinyint(1) DEFAULT '0',
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of category_of_article
-- ----------------------------
BEGIN;
INSERT INTO `category_of_article` VALUES (1, 'Education', 1, '2020-05-30 10:00:00', '2020-05-30 23:56:31');
INSERT INTO `category_of_article` VALUES (2, 'Culture', 1, '2020-05-30 10:00:00', '2020-05-31 00:01:51');
INSERT INTO `category_of_article` VALUES (3, 'Sport', 0, '2020-05-30 10:00:44', '2021-01-08 23:38:41');
INSERT INTO `category_of_article` VALUES (4, 'History', 1, '2020-05-30 10:00:44', '2020-05-31 00:04:14');
INSERT INTO `category_of_article` VALUES (5, 'Varia', 1, '2020-05-30 10:00:44', '2020-05-31 00:05:13');
INSERT INTO `category_of_article` VALUES (6, 'Info', 0, '2020-06-24 20:01:41', '2020-06-24 20:01:44');
INSERT INTO `category_of_article` VALUES (8, 'Tervis', 1, '2020-06-24 10:08:00', '2021-01-08 23:49:08');
COMMIT;

-- ----------------------------
-- Table structure for category_of_news
-- ----------------------------
DROP TABLE IF EXISTS `category_of_news`;
CREATE TABLE `category_of_news` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_enabled` tinyint(1) DEFAULT '0',
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of category_of_news
-- ----------------------------
BEGIN;
INSERT INTO `category_of_news` VALUES (1, 'Politics', 1, '2020-09-12 11:00:00', '2020-09-18 01:17:29');
INSERT INTO `category_of_news` VALUES (2, 'Life', 1, '2020-09-12 11:00:00', '2020-09-18 01:18:42');
INSERT INTO `category_of_news` VALUES (3, 'Education', 1, '2020-09-12 11:00:00', '2020-09-18 01:18:03');
INSERT INTO `category_of_news` VALUES (4, 'Business', 1, '2020-09-13 00:00:00', NULL);
INSERT INTO `category_of_news` VALUES (5, 'Health', 1, '2020-08-01 21:29:00', '2020-09-19 00:30:00');
COMMIT;

-- ----------------------------
-- Table structure for content_type
-- ----------------------------
DROP TABLE IF EXISTS `content_type`;
CREATE TABLE `content_type` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `tabs_text` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `class_names` varchar(255) DEFAULT NULL,
  `backend_template_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `fronted_template_url` varchar(255) DEFAULT NULL,
  `is_enabled` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of content_type
-- ----------------------------
BEGIN;
INSERT INTO `content_type` VALUES (1, 'Home page', NULL, 'HomeEditPanel', NULL, NULL, 1);
INSERT INTO `content_type` VALUES (2, 'Article', 'Edit article', 'ArticleEditPanel', NULL, NULL, 1);
INSERT INTO `content_type` VALUES (3, 'News', 'Edit news', 'NewsEditPanel', NULL, NULL, 1);
INSERT INTO `content_type` VALUES (4, 'Gallery', 'Edit gallery', 'GalleryEditPanel', NULL, NULL, 0);
INSERT INTO `content_type` VALUES (5, 'Events calendar', 'Edit events calendar', 'EventsCalendarEditPanel', NULL, NULL, 0);
INSERT INTO `content_type` VALUES (6, 'Sports calendar', 'Edit sports calendar ', 'SportsCalendarEditPanel', NULL, NULL, 0);
INSERT INTO `content_type` VALUES (7, 'Internal page link', 'Edit internal page link', 'InternalPageEditPanel', NULL, NULL, 1);
INSERT INTO `content_type` VALUES (8, 'Redirecting link', 'Edit redirecting link', 'RedirectingEditPanel', NULL, NULL, 1);
INSERT INTO `content_type` VALUES (9, 'Placeholder', 'Edit placeholder', 'PlaceholderEditPanel', NULL, NULL, 1);
INSERT INTO `content_type` VALUES (10, 'Error Page', 'Edit error page', 'ErrorPageEditPanel', NULL, NULL, 1);
COMMIT;

-- ----------------------------
-- Table structure for error_pages
-- ----------------------------
DROP TABLE IF EXISTS `error_pages`;
CREATE TABLE `error_pages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `menu_content_id` int unsigned DEFAULT NULL,
  `error_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `title_slug` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menu_content_id_idx` (`menu_content_id`) USING BTREE,
  CONSTRAINT `error_pages_ibfk_2` FOREIGN KEY (`menu_content_id`) REFERENCES `menu_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of error_pages
-- ----------------------------
BEGIN;
INSERT INTO `error_pages` VALUES (2, 280, 'The page is under construction', 'the-page-is-under-construction', '<p>Sorry, this page is under construction.</p>\n', '2020-09-09 21:37:01', '2020-11-28 23:39:55');
COMMIT;

-- ----------------------------
-- Table structure for items_per_page
-- ----------------------------
DROP TABLE IF EXISTS `items_per_page`;
CREATE TABLE `items_per_page` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `items_per` varchar(3) COLLATE utf8mb4_general_ci NOT NULL,
  `items_per_num` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of items_per_page
-- ----------------------------
BEGIN;
INSERT INTO `items_per_page` VALUES (1, '10', 10);
INSERT INTO `items_per_page` VALUES (2, '25', 25);
INSERT INTO `items_per_page` VALUES (3, '50', 50);
INSERT INTO `items_per_page` VALUES (4, '100', 100);
COMMIT;

-- ----------------------------
-- Table structure for menu
-- ----------------------------
DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int DEFAULT NULL,
  `depth` int DEFAULT '0',
  `left` int DEFAULT NULL,
  `right` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=427 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of menu
-- ----------------------------
BEGIN;
INSERT INTO `menu` VALUES (1, NULL, 0, 2, 3);
INSERT INTO `menu` VALUES (27, NULL, 0, 4, 11);
INSERT INTO `menu` VALUES (32, 27, 1, 9, 10);
INSERT INTO `menu` VALUES (33, 27, 1, 5, 6);
INSERT INTO `menu` VALUES (34, 27, 1, 7, 8);
INSERT INTO `menu` VALUES (247, NULL, 0, 14, 15);
INSERT INTO `menu` VALUES (248, NULL, 0, 16, 17);
INSERT INTO `menu` VALUES (249, NULL, 0, 18, 35);
INSERT INTO `menu` VALUES (250, 249, 1, 19, 20);
INSERT INTO `menu` VALUES (251, 249, 1, 21, 22);
INSERT INTO `menu` VALUES (252, 249, 1, 23, 24);
INSERT INTO `menu` VALUES (253, 249, 1, 25, 26);
INSERT INTO `menu` VALUES (263, 249, 1, 27, 34);
INSERT INTO `menu` VALUES (264, 263, 2, 28, 33);
INSERT INTO `menu` VALUES (265, 264, 3, 29, 32);
INSERT INTO `menu` VALUES (280, 265, 4, 30, 31);
INSERT INTO `menu` VALUES (281, NULL, 0, 12, 13);
INSERT INTO `menu` VALUES (426, NULL, 0, 36, 37);
COMMIT;

-- ----------------------------
-- Table structure for menu_content
-- ----------------------------
DROP TABLE IF EXISTS `menu_content`;
CREATE TABLE `menu_content` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` int unsigned DEFAULT NULL,
  `menu_text` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `redirect_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `homely_url` int unsigned DEFAULT NULL,
  `is_redirect` int unsigned DEFAULT NULL,
  `selected_page_id` int unsigned DEFAULT NULL,
  `target_type` int unsigned DEFAULT NULL,
  `content_type` int unsigned DEFAULT NULL,
  `is_enabled` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `menu_id_idx` (`menu_id`) USING BTREE,
  KEY `content_type_idx` (`content_type`) USING BTREE,
  KEY `target_type_idx` (`target_type`) USING BTREE,
  KEY `selected_page_id_idx` (`selected_page_id`) USING BTREE,
  CONSTRAINT `content_type_menu_content_fk` FOREIGN KEY (`content_type`) REFERENCES `content_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `menu_id_menu_content_fk` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `selected_page_id_fk` FOREIGN KEY (`selected_page_id`) REFERENCES `menu_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `target_type_menu_content_fk` FOREIGN KEY (`target_type`) REFERENCES `target_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=427 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of menu_content
-- ----------------------------
BEGIN;
INSERT INTO `menu_content` VALUES (1, 1, 'Home', '/', 1, NULL, NULL, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (27, 27, 'Organisation', '/organisation/more-about-the-organisation', 1, NULL, NULL, NULL, 2, 1);
INSERT INTO `menu_content` VALUES (32, 32, 'Contacts', '/contacts/kontaktist-saab-rohkem-teada', 1, NULL, NULL, NULL, 2, 1);
INSERT INTO `menu_content` VALUES (33, 33, 'Board', '/board/juhatusest-lahemalt', 1, NULL, NULL, NULL, 2, 1);
INSERT INTO `menu_content` VALUES (34, 34, 'Statues', 'https://qcubed.eu', NULL, 1, NULL, 1, 8, 1);
INSERT INTO `menu_content` VALUES (247, 247, 'Gallery', 'https://www.google.com', NULL, 1, NULL, 1, 8, 1);
INSERT INTO `menu_content` VALUES (248, 248, 'Projects', '/projects/projektide-arhiiv', 1, NULL, NULL, NULL, 2, 1);
INSERT INTO `menu_content` VALUES (249, 249, 'Cities', '#', 1, NULL, NULL, NULL, 9, 1);
INSERT INTO `menu_content` VALUES (250, 250, 'Tallinn', '/tallinn/tallinna-teenused', 1, NULL, NULL, NULL, 2, 1);
INSERT INTO `menu_content` VALUES (251, 251, 'Tartu', '/statues', 1, 2, 34, NULL, 7, 1);
INSERT INTO `menu_content` VALUES (252, 252, 'Türi', '/turi/parnu-teenused', 1, NULL, NULL, NULL, 2, 1);
INSERT INTO `menu_content` VALUES (253, 253, 'Rakvere', 'https://talkur.ee', NULL, 1, NULL, 1, 8, 1);
INSERT INTO `menu_content` VALUES (263, 263, 'Paide', '/paide/paide-teenused', 1, NULL, NULL, NULL, 2, 1);
INSERT INTO `menu_content` VALUES (264, 264, 'Narva', '/board/juhatusest-lahemalt', 1, 2, 33, NULL, 7, 1);
INSERT INTO `menu_content` VALUES (265, 265, 'Kohtla-Järve', '/gallery', 1, 2, 247, NULL, 7, 1);
INSERT INTO `menu_content` VALUES (280, 280, 'Jõhvi', '/johvi/the-page-is-under-construction', 1, NULL, NULL, NULL, 10, 1);
INSERT INTO `menu_content` VALUES (281, 281, 'News', '/news', 1, NULL, NULL, NULL, 3, 0);
INSERT INTO `menu_content` VALUES (426, 426, 'TEST', '/contacts/kontaktist-saab-rohkem-teada', 1, 2, 32, NULL, 7, 0);
COMMIT;

-- ----------------------------
-- Table structure for metadata
-- ----------------------------
DROP TABLE IF EXISTS `metadata`;
CREATE TABLE `metadata` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `menu_content_id` int unsigned DEFAULT NULL,
  `keywords` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `author` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menu_content_id_idx` (`menu_content_id`) USING BTREE,
  CONSTRAINT `menu_content_id_metadata_f` FOREIGN KEY (`menu_content_id`) REFERENCES `menu_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of metadata
-- ----------------------------
BEGIN;
INSERT INTO `metadata` VALUES (1, 1, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (3, 27, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (14, 33, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (21, 32, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (31, 250, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (32, 252, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (33, 248, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (41, 263, '', '', '');
INSERT INTO `metadata` VALUES (44, 281, NULL, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for news
-- ----------------------------
DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `news_category_id` int unsigned DEFAULT NULL,
  `category` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `title_slug` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `picture` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `picture_description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `author_source` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `use_publication_date` tinyint(1) DEFAULT '0',
  `available_from` datetime DEFAULT NULL,
  `expiry_date` datetime DEFAULT NULL,
  `assigned_by_user` int unsigned DEFAULT NULL,
  `author` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` int unsigned DEFAULT '2',
  `confirmation_asking` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `news_category_id_idx` (`news_category_id`) USING BTREE,
  KEY `post_date_idx` (`post_date`) USING BTREE,
  KEY `available_from_idx` (`available_from`) USING BTREE,
  KEY `status_idx` (`status`) USING BTREE,
  KEY `user_id_idx` (`assigned_by_user`) USING BTREE,
  CONSTRAINT `news-ibfk_2` FOREIGN KEY (`assigned_by_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `news_ibfk_1` FOREIGN KEY (`news_category_id`) REFERENCES `category_of_news` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `news_ibfk_3` FOREIGN KEY (`status`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=148 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of news
-- ----------------------------
BEGIN;
INSERT INTO `news` VALUES (1, 'Isamaale teevad muret EKRE plaanid kaitse-eelarvega', 1, 'Politics', '/news/isamaale-teevad-muret-ekre-plaanid-kaitse-eelarvega', NULL, NULL, NULL, '', '2020-09-18 00:00:00', '2020-10-16 02:02:25', 0, NULL, NULL, 9, 'John Doe', 3, 0);
INSERT INTO `news` VALUES (2, 'Euroopa Komisjon kutsus kiirendama CO2 heitmete vähendamist ', 4, 'Business', '/news/euroopa-komisjon-kutsus-kiirendama-co2-heitmete-vahendamist', NULL, NULL, NULL, NULL, '2020-09-18 18:00:00', '2021-01-07 21:04:14', 0, NULL, NULL, 11, 'Samantha Jones', 1, 0);
INSERT INTO `news` VALUES (3, 'Plaan Kadriorus puumaja asemele uusarendus rajada pahandab kohalikke', 1, 'Politics', '/news/plaan-kadriorus-puumaja-asemele-uusarendus-rajada-pahandab-kohalikke', NULL, NULL, NULL, NULL, '2020-09-19 00:05:34', '2021-01-07 21:04:36', 0, NULL, NULL, 10, 'Alex Smith', 1, 0);
INSERT INTO `news` VALUES (4, 'Abilinnapea: Tallinn ei saada lapsi massiliselt koju ', 3, 'Education', '/news/abilinnapea-tallinn-ei-saada-lapsi-massiliselt-koju', NULL, NULL, NULL, NULL, '2020-09-20 00:15:10', '2020-09-28 21:45:35', 0, NULL, NULL, 12, 'Brett Carlisle', 2, 0);
INSERT INTO `news` VALUES (5, 'Tartul on koolidega Tallinnast veidi erinev lähenemine', 2, 'Life', '/news/tartul-on-koolidega-tallinnast-veidi-erinev-lahenemine', NULL, NULL, NULL, '<p>fghjkl&ouml; dfghjk</p>\n', '2020-09-17 00:23:24', '2021-01-19 01:32:18', 1, '2021-01-31 00:00:00', NULL, 9, 'John Doe', 4, 0);
INSERT INTO `news` VALUES (6, 'Ööpäevaga lisandus 23 uut nakatunut', 1, 'Politics', '/news/oopaevaga-lisandus-23-uut-nakatunut', NULL, NULL, NULL, NULL, '2020-09-17 00:31:19', '2021-01-19 01:33:09', 1, '2021-01-31 00:00:00', NULL, 9, 'John Doe', 4, 0);
INSERT INTO `news` VALUES (7, 'Peipsiääre vald kehtestas eriolukorraga võrreldavad piirangud\'', 3, 'Education', '/news/peipsiaare-vald-kehtestas-eriolukorraga-vorreldavad-piirangud', NULL, NULL, NULL, NULL, '2020-09-18 00:32:58', '2021-01-07 21:04:24', 0, NULL, NULL, 11, 'Samantha Jones', 1, 0);
INSERT INTO `news` VALUES (109, 'Terviseamet: Euroopa kumulatiivne nakkusnäit lähinädalatel kasvab', 5, 'Health', '/news/terviseamet-euroopa-kumulatiivne-nakkusnait-lahinadalatel-kasvab', NULL, NULL, NULL, '<p>sdfghj qwerghjk</p>\n', '2020-09-28 23:36:03', '2021-01-17 01:30:43', 1, '2021-01-30 23:00:00', NULL, 9, 'John Doe', 4, 0);
INSERT INTO `news` VALUES (110, 'Teadlane: Eesti on teise koroonalaine algusfaasis ', 5, 'Health', '/news/teadlane-eesti-on-teise-koroonalaine-algusfaasis', NULL, NULL, NULL, '<p>wertyui sdfghjkl dfghjkl</p>\n', '2020-09-29 01:40:54', '2021-01-03 23:21:39', 1, '2020-10-31 00:00:30', '2020-12-31 00:00:20', 9, 'John Doe', 4, 0);
INSERT INTO `news` VALUES (112, 'Terviseamet: olukord on vaatamata nakatumiste kasvule kontrolli all', 1, 'Politics', '/news/terviseamet-olukord-on-vaatamata-nakatumiste-kasvule-kontrolli-all', NULL, NULL, NULL, '<p>Viimase &ouml;&ouml;p&auml;evaga lisandus 67 positiivset koroonatesti, anal&uuml;&uuml;se tehti pea 2900. Terviseameti nakkushaiguste seire ja epideemiat&otilde;rje osakonna peaspetsialist Hanna Sepp &uuml;tles positiivsete koroonatestide k&otilde;rge arvu kohta, et see oli eeldatav.</p>\n\n<p>&nbsp;</p>\n', '2020-09-29 02:23:56', '2021-02-13 04:10:17', 0, NULL, NULL, 9, 'John Doe', 1, 0);
INSERT INTO `news` VALUES (147, 'Blaaaa', 2, 'Life', '/news/blaaaa', NULL, NULL, NULL, '<p>23rtyuil qwertyuio 234567uio&ouml;</p>\n', '2021-02-18 23:39:17', '2021-02-18 23:39:49', 0, NULL, NULL, 10, 'Alex Smith', 3, 0);
COMMIT;

-- ----------------------------
-- Table structure for news_editors_assn
-- ----------------------------
DROP TABLE IF EXISTS `news_editors_assn`;
CREATE TABLE `news_editors_assn` (
  `news_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  PRIMARY KEY (`news_id`,`user_id`),
  KEY `news_id_idx` (`news_id`) USING BTREE,
  KEY `news_users_assn_2` (`user_id`),
  CONSTRAINT `news_users_assn_1` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `news_users_assn_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of news_editors_assn
-- ----------------------------
BEGIN;
INSERT INTO `news_editors_assn` VALUES (1, 10);
INSERT INTO `news_editors_assn` VALUES (2, 9);
INSERT INTO `news_editors_assn` VALUES (2, 10);
INSERT INTO `news_editors_assn` VALUES (2, 12);
INSERT INTO `news_editors_assn` VALUES (3, 9);
INSERT INTO `news_editors_assn` VALUES (3, 12);
INSERT INTO `news_editors_assn` VALUES (5, 10);
INSERT INTO `news_editors_assn` VALUES (5, 11);
INSERT INTO `news_editors_assn` VALUES (6, 10);
INSERT INTO `news_editors_assn` VALUES (6, 11);
INSERT INTO `news_editors_assn` VALUES (7, 9);
INSERT INTO `news_editors_assn` VALUES (110, 11);
INSERT INTO `news_editors_assn` VALUES (112, 10);
INSERT INTO `news_editors_assn` VALUES (112, 12);
COMMIT;

-- ----------------------------
-- Table structure for status
-- ----------------------------
DROP TABLE IF EXISTS `status`;
CREATE TABLE `status` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `is_enabled` int NOT NULL,
  `written_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '2',
  `drawn_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `visibility` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of status
-- ----------------------------
BEGIN;
INSERT INTO `status` VALUES (1, 1, 'Published', '<i class=\"fa fa-circle fa-lg\" aria-hidden=\"true\" style=\"color: #449d44; line-height: .1;\"></i>  Published', 1);
INSERT INTO `status` VALUES (2, 2, 'Hidden', '<i class=\"fa fa-circle fa-lg\" aria-hidden=\"true\" style=\"color: #ff0000; line-height: .1;\"></i> Hidden', 1);
INSERT INTO `status` VALUES (3, 3, 'Draft', '<i class=\"fa fa-circle-o fa-lg\" aria-hidden=\"true\" style=\"color: #000000; line-height: .1;\"></i> Draft', 1);
INSERT INTO `status` VALUES (4, 4, 'Waiting...', '<i class=\"fa fa-circle fa-lg\" aria-hidden=\"true\" style=\"color: #ffb00c; line-height: .1;\"></i> Waiting...', 0);
COMMIT;

-- ----------------------------
-- Table structure for target_type
-- ----------------------------
DROP TABLE IF EXISTS `target_type`;
CREATE TABLE `target_type` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `target` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of target_type
-- ----------------------------
BEGIN;
INSERT INTO `target_type` VALUES (1, 'New Window (_blank)', '_blank');
INSERT INTO `target_type` VALUES (2, 'Topmost Window (_top)', '_top');
INSERT INTO `target_type` VALUES (3, 'Same Window (_self)', '_self');
INSERT INTO `target_type` VALUES (4, 'Parent Window (_parent)', '_parent');
COMMIT;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `display_real_name_flag` tinyint(1) DEFAULT '0',
  `display_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `items_per_page_by_assigned_user` int unsigned NOT NULL,
  `is_enabled` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_idx` (`username`) USING BTREE,
  KEY `first_name_idx` (`first_name`) USING BTREE,
  KEY `last_name_idx` (`last_name`) USING BTREE,
  KEY `items_per_page_by_assigned_user_idx` (`items_per_page_by_assigned_user`) USING BTREE,
  CONSTRAINT `items_per_page_by_assigned_user_fk` FOREIGN KEY (`items_per_page_by_assigned_user`) REFERENCES `items_per_page` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of user
-- ----------------------------
BEGIN;
INSERT INTO `user` VALUES (9, 'John', 'Doe', 'doe@gmail.com', 'johndoe', NULL, 0, NULL, 1, 1);
INSERT INTO `user` VALUES (10, 'Alex', 'Smith', 'smith@gmail.com', 'alexsmith', NULL, 0, NULL, 3, 1);
INSERT INTO `user` VALUES (11, 'Samantha', 'Jones', 'samanthajones@gmail.com', 'samantha', NULL, 0, NULL, 1, 1);
INSERT INTO `user` VALUES (12, 'Brett', 'Carlisle', 'carlisle@gmail.com', 'carlisle', NULL, 0, NULL, 2, 1);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
