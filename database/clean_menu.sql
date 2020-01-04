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

 Date: 28/06/2019 11:42:24
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
