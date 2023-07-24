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

 Date: 24/06/2023 18:17:44
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for tbl_admin_fts
-- ----------------------------
DROP TABLE IF EXISTS `tbl_admin_fts`;
CREATE TABLE `tbl_admin_fts` (
  `afts_adm_id` bigint(19) unsigned NOT NULL,
  `afts_adm_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `afts_kw` text COLLATE utf8mb4_unicode_ci DEFAULT '',
  PRIMARY KEY (`afts_adm_id`),
  FULLTEXT KEY `FTS_kw` (`afts_kw`)
) ENGINE=Mroonga DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of tbl_admin_fts
-- ----------------------------
BEGIN;
INSERT INTO `tbl_admin_fts` (`afts_adm_id`, `afts_adm_code`, `afts_kw`) VALUES (1, 'biDaf7T3lljdV1438KU', 'biDaf7T3lljdV1438KU Thiều Sỹ Tùng tung0963002862__1gmail__11com 963002862');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
