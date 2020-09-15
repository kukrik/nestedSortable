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

 Date: 15/09/2020 17:39:07
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
  `confirmation_asking` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `menu_content_id_idx` (`menu_content_id`) USING BTREE,
  KEY `category_id_idx` (`category_id`) USING BTREE,
  CONSTRAINT `category_id_article_f` FOREIGN KEY (`category_id`) REFERENCES `category_of_article` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `menu_content_id_article_f` FOREIGN KEY (`menu_content_id`) REFERENCES `menu_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of article
-- ----------------------------
BEGIN;
INSERT INTO `article` VALUES (3, 27, 'More about the organisation', 1, 'more-about-the-organisation', NULL, NULL, NULL, '<p>Tere, head s&otilde;brad &uuml;le Eesti!</p>\n\n<p>Meie loodame, et teie k&otilde;ik p&uuml;site kodus selle n&auml;dalavahetuse ja esimesed 2 n&auml;dalat, nii et saame koroonaviiruse pandeemiast ruttu lahti. See on meie k&otilde;igi enda teha.</p>\n\n<p>Meieni on j&otilde;udnud paljude kurtide inimeste suur mure, et mis saab puude pikendamisest, just nendel inimestel, kellel l&otilde;peb puue m&auml;rtsis-aprillis-mais. Seoses koroonaviiruse pandeemiaga pikendas Sotsiaalkindlustusamet puude kehtivust 6 kuuni. Nii et praegu pole vaja muretseda.</p>\n\n<p>Kui kriisiaeg l&otilde;peb, siis tuleb ikkagi esimesel v&otilde;imalusel puuet pikendada. Aga t&ouml;&ouml;v&otilde;ime hindamist tuleb ikkagi teha, loomulikult kaugt&otilde;lke kaudu.</p>\n\n<p><span class=\"marker\">Kuidas meie saame sellises olukorras teha?</span></p>\n\n<p>Siin tuleb teha selgeks erinevused SKA Skype kaugt&otilde;lketeenuse ja Viipekeelet&otilde;lkide Osa&uuml;hing Skype kaugt&otilde;lketeenuse vahel. SKA kaugt&otilde;lketeenus on sissehelistamisega &uuml;le Eesti, Viipekeelet&otilde;lkide Osa&uuml;hingu teenus on ettetellitav millise situatsiooni kirjeldamise ja aja broneerimisega. SKA ei tasu sellise teenuse eest. Teenuse tasustamine toimub samadel alustel nagu tavat&otilde;lketeenuse tasustamine.</p>\n\n<p>Palun tutvuge antud linkidega!</p>\n', '2020-08-13 16:53:02', '2020-09-12 01:25:19', 1);
INSERT INTO `article` VALUES (14, 33, 'Juhatusest lähemalt', NULL, 'juhatusest-lahemalt', NULL, NULL, NULL, NULL, '2020-08-23 12:59:39', '2020-09-08 22:05:22', 0);
INSERT INTO `article` VALUES (21, 32, 'Kontaktist saab rohkem teada', NULL, 'kontaktist-saab-rohkem-teada', NULL, NULL, NULL, NULL, '2020-09-04 14:29:45', '2020-09-04 14:38:23', 1);
INSERT INTO `article` VALUES (31, 250, 'Tallinna teenused', 8, 'tallinna-teenused', NULL, NULL, NULL, NULL, '2020-09-06 07:59:22', '2020-09-06 07:59:41', 0);
INSERT INTO `article` VALUES (32, 252, 'Pärnu teenused', NULL, 'parnu-teenused', NULL, NULL, NULL, NULL, '2020-09-06 08:03:55', '2020-09-08 21:55:17', 0);
INSERT INTO `article` VALUES (33, 248, 'Projektide arhiiv', NULL, 'projektide-arhiiv', NULL, NULL, NULL, NULL, '2020-09-06 08:06:13', '2020-09-06 10:53:00', 0);
INSERT INTO `article` VALUES (41, 263, 'Paide teenused', NULL, 'paide-teenused', NULL, NULL, NULL, NULL, '2020-09-08 04:38:15', '2020-09-08 04:40:17', 0);
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
INSERT INTO `category_of_article` VALUES (3, 'Sport', 1, '2020-05-30 10:00:44', '2020-05-31 00:03:17');
INSERT INTO `category_of_article` VALUES (4, 'History', 1, '2020-05-30 10:00:44', '2020-05-31 00:04:14');
INSERT INTO `category_of_article` VALUES (5, 'Varia', 1, '2020-05-30 10:00:44', '2020-05-31 00:05:13');
INSERT INTO `category_of_article` VALUES (6, 'Info', 0, '2020-06-24 20:01:41', '2020-06-24 20:01:44');
INSERT INTO `category_of_article` VALUES (8, 'Tervis', 1, '2020-06-24 10:08:00', NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of category_of_news
-- ----------------------------
BEGIN;
INSERT INTO `category_of_news` VALUES (1, 'Poliitika', 1, '2020-09-12 11:00:00', NULL);
INSERT INTO `category_of_news` VALUES (2, 'Eluolu', 1, '2020-09-12 11:00:00', NULL);
INSERT INTO `category_of_news` VALUES (3, 'Haridus', 1, '2020-09-12 11:00:00', NULL);
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of content_type
-- ----------------------------
BEGIN;
INSERT INTO `content_type` VALUES (1, 'Home page', NULL, 'HomeEditPanel', NULL, NULL);
INSERT INTO `content_type` VALUES (2, 'Article', 'Edit article', 'ArticleEditPanel', NULL, NULL);
INSERT INTO `content_type` VALUES (3, 'News', 'Edit news', 'NewsEditPanel', NULL, NULL);
INSERT INTO `content_type` VALUES (4, 'Gallery', 'Edit gallery', 'GalleryEditPanel', NULL, NULL);
INSERT INTO `content_type` VALUES (5, 'Events calendar', 'Edit events calendar', 'EventsCalendarEditPanel', NULL, NULL);
INSERT INTO `content_type` VALUES (6, ' Sports calendar', 'Edit sports calendar ', 'SportsCalendarEditPanel', NULL, NULL);
INSERT INTO `content_type` VALUES (7, 'Internal page link', 'Edit internal page link', 'InternalPageEditPanel', NULL, NULL);
INSERT INTO `content_type` VALUES (8, 'Redirecting link', 'Edit redirecting link', 'RedirectingEditPanel', NULL, NULL);
INSERT INTO `content_type` VALUES (9, 'Placeholder', 'Edit placeholder', 'PlaceholderEditPanel', NULL, NULL);
INSERT INTO `content_type` VALUES (10, 'Error Page', 'Edit error page', 'ErrorPageEditPanel', NULL, NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of error_pages
-- ----------------------------
BEGIN;
INSERT INTO `error_pages` VALUES (2, 280, 'The page is under construction', 'the-page-is-under-construction', '<p>Sorry, this page is under construction.</p>\n', '2020-09-09 21:37:01', '2020-09-12 01:35:17');
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
) ENGINE=InnoDB AUTO_INCREMENT=283 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of menu
-- ----------------------------
BEGIN;
INSERT INTO `menu` VALUES (1, NULL, 0, 2, 3);
INSERT INTO `menu` VALUES (27, NULL, 0, 4, 11);
INSERT INTO `menu` VALUES (32, 27, 1, 5, 6);
INSERT INTO `menu` VALUES (33, 27, 1, 7, 8);
INSERT INTO `menu` VALUES (34, 27, 1, 9, 10);
INSERT INTO `menu` VALUES (246, NULL, 0, 12, 13);
INSERT INTO `menu` VALUES (247, NULL, 0, 14, 15);
INSERT INTO `menu` VALUES (248, NULL, 0, 16, 17);
INSERT INTO `menu` VALUES (249, NULL, 0, 18, 33);
INSERT INTO `menu` VALUES (250, 249, 1, 19, 20);
INSERT INTO `menu` VALUES (251, 249, 1, 21, 22);
INSERT INTO `menu` VALUES (252, 249, 1, 23, 24);
INSERT INTO `menu` VALUES (253, 249, 1, 25, 26);
INSERT INTO `menu` VALUES (263, 249, 1, 27, 28);
INSERT INTO `menu` VALUES (264, 249, 1, 29, 30);
INSERT INTO `menu` VALUES (265, 249, 1, 31, 32);
INSERT INTO `menu` VALUES (280, NULL, 0, 34, 35);
INSERT INTO `menu` VALUES (281, NULL, 0, 36, 37);
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
  `is_redirect` int unsigned DEFAULT NULL,
  `selected_page_id` int unsigned DEFAULT NULL,
  `target_type` int unsigned DEFAULT NULL,
  `content_type` int unsigned DEFAULT NULL,
  `news_type` int unsigned DEFAULT NULL,
  `is_enabled` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `menu_id_idx` (`menu_id`) USING BTREE,
  KEY `content_type_idx` (`content_type`) USING BTREE,
  KEY `target_type_idx` (`target_type`) USING BTREE,
  KEY `selected_page_id_idx` (`selected_page_id`) USING BTREE,
  KEY `news_type_idx` (`news_type`) USING BTREE,
  CONSTRAINT `content_type_menu_content_fk` FOREIGN KEY (`content_type`) REFERENCES `content_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `menu_id_menu_content_fk` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `news_type_menu_content_fk` FOREIGN KEY (`news_type`) REFERENCES `news_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `selected_page_id_fk` FOREIGN KEY (`selected_page_id`) REFERENCES `menu_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `target_type_menu_content_fk` FOREIGN KEY (`target_type`) REFERENCES `target_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=283 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of menu_content
-- ----------------------------
BEGIN;
INSERT INTO `menu_content` VALUES (1, 1, 'Home', '/', NULL, NULL, NULL, 1, NULL, 1);
INSERT INTO `menu_content` VALUES (27, 27, 'Organisation', '/organisation/more-about-the-organisation', NULL, NULL, NULL, 2, NULL, 1);
INSERT INTO `menu_content` VALUES (32, 32, 'Contacts', '/contacts/kontaktist-saab-rohkem-teada', NULL, NULL, NULL, 2, NULL, 1);
INSERT INTO `menu_content` VALUES (33, 33, 'Board', '/board/juhatusest-lahemalt', NULL, NULL, NULL, 2, NULL, 1);
INSERT INTO `menu_content` VALUES (34, 34, 'Statues', 'https://qcubed.eu', 1, NULL, 1, 8, NULL, 1);
INSERT INTO `menu_content` VALUES (246, 246, 'News', '/organisation/more-about-the-organisation', 2, 27, NULL, 7, NULL, 1);
INSERT INTO `menu_content` VALUES (247, 247, 'Gallery', 'https://www.google.com', 1, NULL, 2, 8, NULL, 1);
INSERT INTO `menu_content` VALUES (248, 248, 'Projects', '/projects/projektide-arhiiv', NULL, NULL, NULL, 2, NULL, 1);
INSERT INTO `menu_content` VALUES (249, 249, 'Cities', '#', NULL, NULL, NULL, 9, NULL, 1);
INSERT INTO `menu_content` VALUES (250, 250, 'Tallinn', '/tallinn/tallinna-teenused', NULL, NULL, NULL, 2, NULL, 1);
INSERT INTO `menu_content` VALUES (251, 251, 'Tartu', '/statues', 2, 34, NULL, 7, NULL, 1);
INSERT INTO `menu_content` VALUES (252, 252, 'Türi', '/turi/parnu-teenused', NULL, NULL, NULL, 2, NULL, 1);
INSERT INTO `menu_content` VALUES (253, 253, 'Rakvere', 'https://talkur.ee', 1, NULL, 3, 8, NULL, 1);
INSERT INTO `menu_content` VALUES (263, 263, 'Paide', '/paide/paide-teenused', NULL, NULL, NULL, 2, NULL, 1);
INSERT INTO `menu_content` VALUES (264, 264, 'Narva', '/board/juhatusest-lahemalt', 2, 33, NULL, 7, NULL, 1);
INSERT INTO `menu_content` VALUES (265, 265, 'Kohtla-Järve', '/gallery', 2, 247, NULL, 7, NULL, 1);
INSERT INTO `menu_content` VALUES (280, 280, 'Jõhvi', '/johvi/the-page-is-under-construction', NULL, NULL, NULL, 10, NULL, 0);
INSERT INTO `menu_content` VALUES (281, 281, 'Välisuudised', '/news', NULL, NULL, NULL, 3, 3, 0);
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
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
INSERT INTO `metadata` VALUES (41, 263, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (44, 281, NULL, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for news
-- ----------------------------
DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `news_type_id` int unsigned DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `news_category_id` int unsigned DEFAULT NULL,
  `title_slug` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `picture` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `picture_description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `author_source` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `use_publication_date` tinyint DEFAULT NULL,
  `available_from` datetime DEFAULT NULL,
  `expiry_date` datetime DEFAULT NULL,
  `author` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_enabled` int DEFAULT '0',
  `confirmation_asking` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `news_category_id_idx` (`news_category_id`) USING BTREE,
  KEY `news_type_id_idx` (`news_type_id`) USING BTREE,
  KEY `post_date_idx` (`post_date`) USING BTREE,
  KEY `available_from_idx` (`available_from`) USING BTREE,
  KEY `user_id_idx` (`user_id`) USING BTREE,
  CONSTRAINT `news_ibfk_1` FOREIGN KEY (`news_category_id`) REFERENCES `category_of_news` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `news_ibfk_2` FOREIGN KEY (`news_type_id`) REFERENCES `news_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `news_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of news
-- ----------------------------
BEGIN;
INSERT INTO `news` VALUES (1, NULL, 3, 'Isamaale teevad muret EKRE plaanid kaitse-eelarvega', 1, 'isamaale-teevad-muret-ekre-plaanid-kaitse-eelarvega', NULL, NULL, NULL, NULL, '2020-09-11 19:49:12', '2020-09-15 01:32:39', NULL, '2020-09-12 22:15:11', NULL, 'Sergei Matvijenko', 2, 0);
INSERT INTO `news` VALUES (2, NULL, 3, 'Plaan Kadriorus puumaja asemele uusarendus rajada pahandab kohalikke', 2, '/news/plaan-kadriorus-puumaja-asemele-uusarendus-rajada-pahandab-kohalikke', NULL, NULL, NULL, NULL, '2020-09-12 19:25:24', '2020-09-14 20:57:46', NULL, '2020-09-13 19:25:33', NULL, 'Tiit Papp', 4, 0);
INSERT INTO `news` VALUES (3, NULL, 3, 'Abilinnapea: Tallinn ei saada lapsi massiliselt koju ', 3, '/news/abilinnapea-tallinn-ei-saada-lapsi-massiliselt-koju', NULL, NULL, NULL, NULL, '2020-09-12 19:29:07', '2020-09-14 20:57:36', NULL, '2020-09-12 19:29:17', NULL, 'Argo Purv', 1, 0);
INSERT INTO `news` VALUES (4, NULL, 3, 'Tartul on koolidega Tallinnast veidi erinev lähenemine', NULL, 'tartul-on-koolidega-tallinnast-veidi-erinev-lahenemine', NULL, NULL, NULL, '', '2020-09-12 19:41:15', '2020-09-15 01:32:46', NULL, '2020-09-14 15:00:13', NULL, 'Kaido Tiislär', NULL, 0);
INSERT INTO `news` VALUES (5, NULL, 3, 'Ööpäevaga lisandus 23 uut nakatunut', 2, '/news/oopaevaga-lisandus-23-uut-nakatunut', NULL, NULL, NULL, NULL, '2020-09-12 23:49:53', '2020-09-14 22:08:50', NULL, '2020-09-12 23:49:53', NULL, 'Tiit Papp', 1, 0);
INSERT INTO `news` VALUES (6, 1, 3, 'Implementation of a back-office Human Resources system for State College', 2, '/news/implementation-of-a-back-office-human-resources-system-for-state-college', NULL, NULL, NULL, NULL, '2020-09-14 00:27:29', '2020-09-14 20:52:46', NULL, NULL, NULL, 'John Doe', 1, 0);
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
  `first_name` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `display_real_name_flag` tinyint(1) DEFAULT NULL,
  `display_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_enabled` tinyint(1) DEFAULT NULL,
  `desired_items_per_page` int NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_idx` (`username`) USING BTREE,
  KEY `first_name_idx` (`first_name`) USING BTREE,
  KEY `last_name_idx` (`last_name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of user
-- ----------------------------
BEGIN;
INSERT INTO `user` VALUES (1, 'John', 'Doe', NULL, 1, 'John Doe', 1, 10, NULL, 'john');
INSERT INTO `user` VALUES (2, 'Kendall', 'Public', NULL, NULL, 'Kendall Public', 1, 25, NULL, 'kendall');
INSERT INTO `user` VALUES (3, 'Ben', 'Robinson', NULL, NULL, 'ben Robinson', 1, 30, NULL, 'ben');
INSERT INTO `user` VALUES (4, 'Samantha', 'Jones', NULL, NULL, 'Samantha Jones', 1, 50, NULL, 'samantha');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
