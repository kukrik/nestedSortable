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

 Date: 06/15/2018 00:42:56 AM
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `menu`
-- ----------------------------
DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `depth` int(11) NOT NULL DEFAULT '0',
  `left` int(11) NOT NULL,
  `right` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `menu`
-- ----------------------------
BEGIN;
INSERT INTO `menu` VALUES ('1', null, '0', '2', '7'), ('2', '1', '1', '3', '4'), ('3', '1', '1', '5', '6'), ('4', null, '0', '8', '9'), ('5', null, '0', '10', '11'), ('6', null, '0', '12', '13'), ('7', null, '0', '14', '15'), ('8', null, '0', '16', '17');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
