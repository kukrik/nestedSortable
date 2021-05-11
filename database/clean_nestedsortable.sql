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

 Date: 11/05/2021 17:00:22
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for items_per_page
-- ----------------------------
DROP TABLE IF EXISTS `items_per_page`;
CREATE TABLE `items_per_page` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `items_per` varchar(3) COLLATE utf8mb4_general_ci NOT NULL,
  `items_per_num` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of menu
-- ----------------------------
BEGIN;
INSERT INTO `menu` VALUES (1, NULL, 0, 2, 3);
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of menu_content
-- ----------------------------
BEGIN;
INSERT INTO `menu_content` VALUES (1, 1, 'Home', '/', 1, NULL, NULL, NULL, 1, 1);
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of metadata
-- ----------------------------
BEGIN;
INSERT INTO `metadata` VALUES (1, 1, NULL, NULL, NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of user
-- ----------------------------
BEGIN;
INSERT INTO `user` VALUES (1, 'John', 'Doe', 'doe@gmail.com', 'johndoe', NULL, 0, NULL, 1, 1);
INSERT INTO `user` VALUES (2, 'Alex', 'Smith', 'smith@gmail.com', 'alexsmith', NULL, 0, NULL, 3, 1);
INSERT INTO `user` VALUES (3, 'Samantha', 'Jones', 'samanthajones@gmail.com', 'samantha', NULL, 0, NULL, 1, 1);
INSERT INTO `user` VALUES (4, 'Brett', 'Carlisle', 'carlisle@gmail.com', 'carlisle', NULL, 0, NULL, 2, 1);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
