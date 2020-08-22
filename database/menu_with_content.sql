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

 Date: 23/04/2020 20:03:23
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for content_type
-- ----------------------------
DROP TABLE IF EXISTS `content_type`;
CREATE TABLE `content_type` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `template_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of content_type
-- ----------------------------
BEGIN;
INSERT INTO `content_type` VALUES (1, 'Home', NULL);
INSERT INTO `content_type` VALUES (2, 'Content', NULL);
INSERT INTO `content_type` VALUES (3, 'News', NULL);
INSERT INTO `content_type` VALUES (4, 'Gallery', NULL);
INSERT INTO `content_type` VALUES (5, 'Internal page link', NULL);
INSERT INTO `content_type` VALUES (6, 'Redirecting link', NULL);
INSERT INTO `content_type` VALUES (7, 'Error Page', NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

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
INSERT INTO `menu` VALUES (7, NULL, 0, 14, 15);
INSERT INTO `menu` VALUES (8, NULL, 0, 16, 17);
INSERT INTO `menu` VALUES (9, NULL, 0, 18, 19);
INSERT INTO `menu` VALUES (10, NULL, 0, 20, 39);
INSERT INTO `menu` VALUES (11, 10, 1, 21, 22);
INSERT INTO `menu` VALUES (12, 10, 1, 23, 38);
INSERT INTO `menu` VALUES (13, 12, 2, 24, 25);
INSERT INTO `menu` VALUES (14, 12, 2, 26, 27);
INSERT INTO `menu` VALUES (15, 12, 2, 28, 29);
INSERT INTO `menu` VALUES (16, 12, 2, 30, 31);
INSERT INTO `menu` VALUES (17, 12, 2, 32, 33);
INSERT INTO `menu` VALUES (18, 12, 2, 34, 35);
INSERT INTO `menu` VALUES (19, 12, 2, 36, 37);
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
  `content_type` int unsigned DEFAULT NULL,
  `is_enabled` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `menu_id_idx` (`menu_id`) USING BTREE,
  KEY `content_type_idx` (`content_type`) USING BTREE,
  CONSTRAINT `content_type_f` FOREIGN KEY (`content_type`) REFERENCES `content_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `menu_id_f` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of menu_content
-- ----------------------------
BEGIN;
INSERT INTO `menu_content` VALUES (1, 1, 'Home', '', 1, 1);
INSERT INTO `menu_content` VALUES (2, 2, 'Organisation', NULL, 2, 1);
INSERT INTO `menu_content` VALUES (3, 3, 'Contacts', NULL, 2, 1);
INSERT INTO `menu_content` VALUES (4, 4, 'Board', NULL, 2, 1);
INSERT INTO `menu_content` VALUES (5, 5, 'Statues', NULL, 2, 1);
INSERT INTO `menu_content` VALUES (6, 6, 'News', NULL, 3, 1);
INSERT INTO `menu_content` VALUES (7, 7, 'Gallery', NULL, 4, 1);
INSERT INTO `menu_content` VALUES (8, 8, 'Projects', NULL, 2, 1);
INSERT INTO `menu_content` VALUES (9, 9, 'Reporting', NULL, 2, 1);
INSERT INTO `menu_content` VALUES (10, 10, 'Services', NULL, 2, 1);
INSERT INTO `menu_content` VALUES (11, 11, 'Social welfare', NULL, NULL, 1);
INSERT INTO `menu_content` VALUES (12, 12, 'Cities', NULL, NULL, 1);
INSERT INTO `menu_content` VALUES (13, 13, 'Tallinn', NULL, NULL, 1);
INSERT INTO `menu_content` VALUES (14, 14, 'Tartu', NULL, NULL, 1);
INSERT INTO `menu_content` VALUES (15, 15, 'Pärnu', NULL, NULL, 1);
INSERT INTO `menu_content` VALUES (16, 16, 'Paide', NULL, NULL, 1);
INSERT INTO `menu_content` VALUES (17, 17, 'Viljandi', NULL, NULL, 1);
INSERT INTO `menu_content` VALUES (18, 18, 'Võru', NULL, NULL, 1);
INSERT INTO `menu_content` VALUES (19, 19, 'Kuressaare', NULL, NULL, 1);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
