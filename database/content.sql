/*
 Navicat MySQL Data Transfer

 Source Server         : KOHALIK
 Source Server Type    : MySQL
 Source Server Version : 50722
 Source Host           : localhost
 Source Database       : qcubed-4

 Target Server Type    : MySQL
 Target Server Version : 50722
 File Encoding         : utf-8

 Date: 06/15/2018 00:43:07 AM
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `content`
-- ----------------------------
DROP TABLE IF EXISTS `content`;
CREATE TABLE `content` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) DEFAULT NULL,
  `menu_text` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `is_enabled` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `menu_id_idx` (`menu_id`) USING BTREE,
  CONSTRAINT `menu_content_fk_1` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `content`
-- ----------------------------
BEGIN;
INSERT INTO `content` VALUES ('1', '1', 'Home', null, null, '1'), ('2', '2', 'Organisation', null, null, '1'), ('3', '3', 'Contacts', null, null, '1'), ('4', '4', 'Board', null, null, '1'), ('5', '5', 'Statutes', null, null, '1'), ('6', '6', 'Reporting', null, null, '1'), ('7', '7', 'Projects', null, null, '1'), ('8', '8', 'Gallery', null, null, '0');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
