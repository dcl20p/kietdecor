/*
 Navicat Premium Data Transfer

 Source Server         : MariaDB
 Source Server Type    : MariaDB
 Source Server Version : 100701
 Source Host           : localhost:3308
 Source Schema         : db_first_laminas

 Target Server Type    : MariaDB
 Target Server Version : 100701
 File Encoding         : 65001

 Date: 24/06/2023 18:17:30
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for tbl_admin
-- ----------------------------
DROP TABLE IF EXISTS `tbl_admin`;
CREATE TABLE `tbl_admin` (
  `adm_id` bigint(19) NOT NULL AUTO_INCREMENT,
  `adm_code` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `adm_fullname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `adm_email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `adm_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `adm_phone_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `adm_avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `adm_username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `adm_password` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adm_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `adm_create_time` int(12) unsigned DEFAULT 0,
  `adm_last_login_time` int(12) unsigned DEFAULT 0,
  `adm_last_access_time` int(12) unsigned DEFAULT 0,
  `adm_ssid` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `adm_status` smallint(1) DEFAULT 0,
  `adm_groupcode` enum('SUPPORT','SUPPER_ADMIN','MANAGER','TESTER','STAFF') COLLATE utf8mb4_unicode_ci DEFAULT 'STAFF',
  `adm_first_login_time` int(12) unsigned DEFAULT 0,
  `adm_is_del` smallint(1) DEFAULT 0,
  `adm_blocked_time` int(12) unsigned DEFAULT 0,
  `adm_bg_timeline` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `adm_gender` enum('MALE','FEMALE','OTHER') COLLATE utf8mb4_unicode_ci DEFAULT 'MALE',
  `adm_birthday` int(12) unsigned DEFAULT 0,
  `adm_json_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '{"first":"","last":""}',
  PRIMARY KEY (`adm_id`) USING BTREE,
  KEY `IDX_code` (`adm_code`) USING BTREE,
  KEY `IDX_group_code` (`adm_groupcode`) USING BTREE,
  KEY `IDX_ssId` (`adm_ssid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of tbl_admin
-- ----------------------------
BEGIN;
INSERT INTO `tbl_admin` (`adm_id`, `adm_code`, `adm_fullname`, `adm_email`, `adm_phone`, `adm_phone_code`, `adm_avatar`, `adm_username`, `adm_password`, `adm_address`, `adm_create_time`, `adm_last_login_time`, `adm_last_access_time`, `adm_ssid`, `adm_status`, `adm_groupcode`, `adm_first_login_time`, `adm_is_del`, `adm_blocked_time`, `adm_bg_timeline`, `adm_gender`, `adm_birthday`, `adm_json_name`) VALUES (1, 'biDaf7T3lljdV1438KU', 'Thiều Sỹ Tùng', 'tung0963002862@gmail.com', '963002862', '+84', 'zhWbrk3vk7ui516G.jpg', '', '26Rm/urngv2iX1/I1isSuTSc.lAP3i8R7UKSLSSHVWnCFVqHBk7pwroZieXcpodnBRO', '35, chung cư Ngọc Lan, đường Phú Thuận, phường Phú Thuận, quận 7, TPHCM', 1491885621, 1687598917, 1687605332, 'd8a7cbbeb00f48ad5f45c144b72b2927', 1, 'SUPPORT', 1620644751, 0, 0, '874qZ3H969ki51LC.jpg', 'MALE', 793126800, '{\"first\":\"S\\u1ef9 T\\u00f9ng\",\"last\":\"Thi\\u1ec1u\"}');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
