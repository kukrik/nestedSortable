/*
 Navicat MySQL Data Transfer

 Source Server         : KOHALIK
 Source Server Type    : MySQL
 Source Server Version : 50722
 Source Host           : localhost:3306
 Source Schema         : qcubed-4

 Target Server Type    : MySQL
 Target Server Version : 50722
 File Encoding         : 65001

 Date: 28/06/2019 11:41:09
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for content_type
-- ----------------------------
DROP TABLE IF EXISTS `content_type`;
CREATE TABLE `content_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `template_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of content_type
-- ----------------------------
BEGIN;
INSERT INTO `content_type` VALUES (1, 'Home Page', NULL);
INSERT INTO `content_type` VALUES (2, 'Internal Link', NULL);
INSERT INTO `content_type` VALUES (3, 'External Link', NULL);
INSERT INTO `content_type` VALUES (4, 'Error Page', NULL);
INSERT INTO `content_type` VALUES (5, 'Article', NULL);
INSERT INTO `content_type` VALUES (6, 'News', NULL);
INSERT INTO `content_type` VALUES (7, 'Gallery', NULL);
COMMIT;

-- ----------------------------
-- Table structure for menu
-- ----------------------------
DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `depth` int(11) DEFAULT '0',
  `left` int(11) DEFAULT NULL,
  `right` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of menu
-- ----------------------------
BEGIN;
INSERT INTO `menu` VALUES (1, NULL, 0, 2, 3);
INSERT INTO `menu` VALUES (2, NULL, 0, 4, 11);
INSERT INTO `menu` VALUES (3, 2, 1, 5, 6);
INSERT INTO `menu` VALUES (4, 2, 1, 7, 8);
INSERT INTO `menu` VALUES (5, 2, 1, 9, 10);
INSERT INTO `menu` VALUES (6, NULL, 0, 12, 13);
INSERT INTO `menu` VALUES (7, NULL, 0, 14, 19);
INSERT INTO `menu` VALUES (8, 7, 1, 15, 18);
INSERT INTO `menu` VALUES (9, 8, 2, 16, 17);
INSERT INTO `menu` VALUES (11, NULL, 0, 20, 21);
COMMIT;

-- ----------------------------
-- Table structure for menu_content
-- ----------------------------
DROP TABLE IF EXISTS `menu_content`;
CREATE TABLE `menu_content` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) DEFAULT NULL,
  `menu_text` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `menu_type` int(11) DEFAULT NULL,
  `is_enabled` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `menu_id_idx` (`menu_id`) USING BTREE,
  KEY `menu_type_idx` (`menu_type`),
  CONSTRAINT `menu_content_f` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `menu_type_f` FOREIGN KEY (`menu_type`) REFERENCES `content_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of menu_content
-- ----------------------------
BEGIN;
INSERT INTO `menu_content` VALUES (1, 1, 'Home', 1, 1);
INSERT INTO `menu_content` VALUES (2, 2, 'Organisation', 5, 1);
INSERT INTO `menu_content` VALUES (3, 3, 'Contacts', 5, 1);
INSERT INTO `menu_content` VALUES (4, 4, 'Board', 5, 1);
INSERT INTO `menu_content` VALUES (5, 5, 'Statues', 5, 1);
INSERT INTO `menu_content` VALUES (6, 6, 'Reporting', 2, 1);
INSERT INTO `menu_content` VALUES (7, 7, 'Projects', 5, 1);
INSERT INTO `menu_content` VALUES (8, 8, 'News', 6, 0);
INSERT INTO `menu_content` VALUES (9, 9, 'Gallery', 7, 0);
INSERT INTO `menu_content` VALUES (11, 11, 'Tervitusi Pärnust', 3, 0);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
