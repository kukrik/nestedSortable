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

 Date: 03/08/2020 16:53:01
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
  `picture` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `picture_description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL,
  `confirmation_asking` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `menu_content_id_idx` (`menu_content_id`) USING BTREE,
  KEY `category_id_idx` (`category_id`) USING BTREE,
  CONSTRAINT `category_id_article_f` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `menu_content_id_article_f` FOREIGN KEY (`menu_content_id`) REFERENCES `menu_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of article
-- ----------------------------
BEGIN;
INSERT INTO `article` VALUES (1, 1, 'Home', NULL, '', NULL, NULL, NULL, NULL, '2020-06-23 18:11:15', 0);
INSERT INTO `article` VALUES (2, 2, 'More about the organisation', 1, 'more-about-the-organisation', NULL, NULL, '<p>Tere, head s&otilde;brad &uuml;le Eesti!</p>\n\n<p>Meie loodame, et teie k&otilde;ik p&uuml;site kodus selle n&auml;dalavahetuse ja esimesed 2 n&auml;dalat, nii et saame koroonaviiruse pandeemiast ruttu lahti. See on meie k&otilde;igi enda teha.</p>\n\n<p>Meieni on j&otilde;udnud paljude kurtide inimeste suur mure, et mis saab puude pikendamisest, just nendel inimestel, kellel l&otilde;peb puue m&auml;rtsis-aprillis-mais. Seoses koroonaviiruse pandeemiaga pikendas Sotsiaalkindlustusamet puude kehtivust 6 kuuni. Nii et praegu pole vaja muretseda.</p>\n\n<p>Kui kriisiaeg l&otilde;peb, siis tuleb ikkagi esimesel v&otilde;imalusel puuet pikendada. Aga t&ouml;&ouml;v&otilde;ime hindamist tuleb ikkagi teha, loomulikult kaugt&otilde;lke kaudu.</p>\n\n<p><span class=\"marker\">Kuidas meie saame sellises olukorras teha?</span></p>\n\n<p>Siin tuleb teha selgeks erinevused SKA Skype kaugt&otilde;lketeenuse ja Viipekeelet&otilde;lkide Osa&uuml;hing Skype kaugt&otilde;lketeenuse vahel. SKA kaugt&otilde;lketeenus on sissehelistamisega &uuml;le Eesti, Viipekeelet&otilde;lkide Osa&uuml;hingu teenus on ettetellitav millise situatsiooni kirjeldamise ja aja broneerimisega. SKA ei tasu sellise teenuse eest. Teenuse tasustamine toimub samadel alustel nagu tavat&otilde;lketeenuse tasustamine.</p>\n\n<p>Palun tutvuge antud linkidega!</p>\n', '2020-06-09 13:35:00', '2020-07-25 17:56:21', 1);
COMMIT;

-- ----------------------------
-- Table structure for category
-- ----------------------------
DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_enabled` tinyint(1) DEFAULT '0',
  `post_date` datetime DEFAULT NULL,
  `post_update_date` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of category
-- ----------------------------
BEGIN;
INSERT INTO `category` VALUES (1, 'Education', 1, '2020-05-30 10:00:00', '2020-05-30 23:56:31');
INSERT INTO `category` VALUES (2, 'Culture', 1, '2020-05-30 10:00:00', '2020-05-31 00:01:51');
INSERT INTO `category` VALUES (3, 'Sport', 1, '2020-05-30 10:00:44', '2020-05-31 00:03:17');
INSERT INTO `category` VALUES (4, 'History', 1, '2020-05-30 10:00:44', '2020-05-31 00:04:14');
INSERT INTO `category` VALUES (5, 'Varia', 1, '2020-05-30 10:00:44', '2020-05-31 00:05:13');
INSERT INTO `category` VALUES (6, 'Info', 0, '2020-06-24 20:01:41', '2020-06-24 20:01:44');
INSERT INTO `category` VALUES (8, 'Tervis', 1, '2020-06-24 10:08:00', NULL);
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
  `template_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of content_type
-- ----------------------------
BEGIN;
INSERT INTO `content_type` VALUES (1, 'Home', NULL, 'HomeEditPanel', NULL);
INSERT INTO `content_type` VALUES (2, 'Article', 'Edit article', 'ArticleEditPanel', NULL);
INSERT INTO `content_type` VALUES (3, 'News', 'Edit news', 'NewsEditPanel', NULL);
INSERT INTO `content_type` VALUES (4, 'Gallery', 'Edit gallery', 'GalleryEditPanel', NULL);
INSERT INTO `content_type` VALUES (5, 'Internal page link', 'Edit internal page link', 'InternalPageEditPanel', NULL);
INSERT INTO `content_type` VALUES (6, 'Redirecting link', 'Edit redirecting link', 'RedirectingEditPanel', NULL);
INSERT INTO `content_type` VALUES (7, 'Error Page', 'Edit error page', 'ErrorPageEditPanel', NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of menu
-- ----------------------------
BEGIN;
INSERT INTO `menu` VALUES (1, NULL, 0, 2, 3);
INSERT INTO `menu` VALUES (2, NULL, 0, 4, 5);
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of menu_content
-- ----------------------------
BEGIN;
INSERT INTO `menu_content` VALUES (1, 1, 'Home', '/', NULL, NULL, NULL, 1, 1);
INSERT INTO `menu_content` VALUES (2, 2, 'Organisation', '/organisation/more-about-the-organisation', NULL, NULL, NULL, 2, 1);
COMMIT;

-- ----------------------------
-- Table structure for metadata
-- ----------------------------
DROP TABLE IF EXISTS `metadata`;
CREATE TABLE `metadata` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `menu_content_id` int unsigned DEFAULT NULL,
  `keywords` text COLLATE utf8_unicode_ci,
  `description` text COLLATE utf8_unicode_ci,
  `author` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menu_content_id_idx` (`menu_content_id`) USING BTREE,
  CONSTRAINT `menu_content_id_metadata_f` FOREIGN KEY (`menu_content_id`) REFERENCES `menu_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of metadata
-- ----------------------------
BEGIN;
INSERT INTO `metadata` VALUES (1, 1, NULL, NULL, NULL);
INSERT INTO `metadata` VALUES (2, 2, NULL, NULL, NULL);
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

SET FOREIGN_KEY_CHECKS = 1;
