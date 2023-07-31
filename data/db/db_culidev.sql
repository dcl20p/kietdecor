/*
 Navicat Premium Data Transfer

 Source Server         : MariaDB
 Source Server Type    : MariaDB
 Source Server Version : 100701
 Source Host           : localhost:3308
 Source Schema         : db_kietdecor

 Target Server Type    : MariaDB
 Target Server Version : 100701
 File Encoding         : 65001

 Date: 30/07/2023 19:38:04
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for tbl_access_log
-- ----------------------------
DROP TABLE IF EXISTS `tbl_access_log`;
CREATE TABLE `tbl_access_log` (
  `al_id` bigint(19) unsigned NOT NULL AUTO_INCREMENT,
  `al_user_id` bigint(19) unsigned DEFAULT NULL,
  `al_user_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `al_site` enum('MANAGERS','CUSTOMER') COLLATE utf8mb4_unicode_ci DEFAULT 'CUSTOMER',
  `al_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `al_method` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `al_route_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `al_params` text COLLATE utf8mb4_unicode_ci DEFAULT '',
  `al_ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `al_created` int(12) unsigned DEFAULT 0,
  `al_state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `al_content` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  PRIMARY KEY (`al_id`) USING BTREE,
  KEY `IDX_user` (`al_user_id`) USING BTREE,
  KEY `IDX_site` (`al_site`) USING BTREE,
  KEY `IDX_url` (`al_url`) USING BTREE,
  KEY `IDX_method` (`al_method`) USING BTREE,
  KEY `IDX_created` (`al_created`) USING BTREE,
  KEY `IDX_user_code` (`al_user_code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1227 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

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
  `adm_update_time` int(12) unsigned DEFAULT 0,
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for tbl_admin_fts
-- ----------------------------
DROP TABLE IF EXISTS `tbl_admin_fts`;
CREATE TABLE `tbl_admin_fts` (
  `afts_adm_id` bigint(19) unsigned NOT NULL,
  `afts_adm_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `afts_kw` text COLLATE utf8mb4_unicode_ci DEFAULT '',
  PRIMARY KEY (`afts_adm_id`) USING BTREE,
  FULLTEXT KEY `FTS_kw` (`afts_kw`)
) ENGINE=Mroonga DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for tbl_client
-- ----------------------------
DROP TABLE IF EXISTS `tbl_client`;
CREATE TABLE `tbl_client` (
  `client_id` bigint(19) unsigned NOT NULL AUTO_INCREMENT,
  `client_user_id` bigint(19) unsigned DEFAULT NULL,
  `client_user_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `client_area_type` enum('CUSTOMER','MANAGER') COLLATE utf8mb4_unicode_ci DEFAULT 'CUSTOMER',
  `client_enpoint` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `client_auth_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `client_public_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `client_type` enum('ANDROID','IOS','CHROME','FIREFOX') COLLATE utf8mb4_unicode_ci DEFAULT 'CHROME',
  `client_package` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `client_join_time` int(12) unsigned DEFAULT 0,
  `client_device_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  PRIMARY KEY (`client_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for tbl_constant
-- ----------------------------
DROP TABLE IF EXISTS `tbl_constant`;
CREATE TABLE `tbl_constant` (
  `constant_id` bigint(19) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id của dữ liệu',
  `constant_code` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'Mã hằng số',
  `constant_content` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `constant_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `constant_sender` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'Người gửi',
  `constant_receiver` text COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'Danh sách email nhận (cách bởi ";")',
  `constant_note` text COLLATE utf8mb4_unicode_ci DEFAULT '',
  `constant_creation_time` int(12) DEFAULT 0 COMMENT 'Thời gian tạo',
  `constant_last_update_time` int(12) DEFAULT 0 COMMENT 'Lần cập nhật gần nhất',
  `constant_last_update_admin_id` int(12) DEFAULT NULL COMMENT 'User nào cập nhật',
  `constant_type` enum('ADMIN','SYSTEM','HIDDEN') CHARACTER SET utf8mb4 DEFAULT 'ADMIN' COMMENT 'Loại hằng số',
  `constant_mode` enum('HTML','TEXT') COLLATE utf8mb4_unicode_ci DEFAULT 'HTML' COMMENT 'Loại dữ liệu: HTML: editor, TEXT: input',
  `constant_tmpl_type` enum('EMAIL','NOTIFY','ARRAY','TEXT','MAIL_CONFIG','NUMERIC','HTML','EMAIL_ADMIN') COLLATE utf8mb4_unicode_ci DEFAULT 'TEXT',
  `constant_order` int(12) DEFAULT 0,
  `constant_search` varchar(2000) COLLATE utf8mb4_unicode_ci DEFAULT '',
  PRIMARY KEY (`constant_id`) USING BTREE,
  UNIQUE KEY `IDX_code` (`constant_code`) USING BTREE,
  KEY `IDX_user` (`constant_last_update_admin_id`) USING BTREE,
  KEY `IDX_update_time` (`constant_last_update_time`) USING BTREE,
  KEY `IDX_search` (`constant_search`(768)) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='Bảng lưu thông tin hằng số cấu hình hệ thống';

-- ----------------------------
-- Table structure for tbl_error
-- ----------------------------
DROP TABLE IF EXISTS `tbl_error`;
CREATE TABLE `tbl_error` (
  `error_id` bigint(19) unsigned NOT NULL AUTO_INCREMENT,
  `error_user_id` bigint(19) unsigned DEFAULT NULL,
  `error_uri` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `error_params` longtext COLLATE utf8mb4_unicode_ci DEFAULT '',
  `error_method` enum('POST','GET','PUT','DELETE') COLLATE utf8mb4_unicode_ci DEFAULT 'GET',
  `error_msg` longtext COLLATE utf8mb4_unicode_ci DEFAULT '',
  `error_code` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `error_trace` longtext COLLATE utf8mb4_unicode_ci DEFAULT '',
  `error_status` smallint(1) unsigned DEFAULT 0,
  `error_time` int(12) unsigned DEFAULT 0,
  PRIMARY KEY (`error_id`) USING BTREE,
  FULLTEXT KEY `FT_keyword` (`error_uri`,`error_params`,`error_msg`,`error_trace`)
) ENGINE=InnoDB AUTO_INCREMENT=733 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for tbl_fe_menu
-- ----------------------------
DROP TABLE IF EXISTS `tbl_fe_menu`;
CREATE TABLE `tbl_fe_menu` (
  `menu_id` bigint(19) unsigned NOT NULL AUTO_INCREMENT,
  `menu_parent_id` bigint(19) unsigned DEFAULT NULL COMMENT 'Parent menu',
  `menu_adm_id` bigint(19) DEFAULT NULL COMMENT 'Creator',
  `menu_code` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'Code of menu',
  `menu_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'Title',
  `menu_link` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Url',
  `menu_icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'Icon',
  `menu_style` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Style (css)',
  `menu_type` enum('TEXT','LINK','INPUT','IMAGE','IMAGE_LINK','REFER_LINK') COLLATE utf8mb4_unicode_ci DEFAULT 'LINK',
  `menu_order` int(12) DEFAULT 0 COMMENT 'Order',
  `menu_status` smallint(1) DEFAULT 0 COMMENT 'State: Display or hidden',
  `menu_level` smallint(3) DEFAULT 0 COMMENT 'Level',
  `menu_time` int(12) DEFAULT 0 COMMENT 'Created time',
  `menu_edit_time` int(12) DEFAULT 0 COMMENT 'Last edit time',
  `menu_edit_adm_id` bigint(19) DEFAULT NULL COMMENT 'Last administrator edit',
  `menu_is_login` enum('ALL','YES','NO') COLLATE utf8mb4_unicode_ci DEFAULT 'ALL',
  `menu_has_post` smallint(1) DEFAULT 0,
  `menu_position` enum('HEADER','FOOTER','CTM_FAQ') COLLATE utf8mb4_unicode_ci DEFAULT 'HEADER',
  `menu_domain` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `menu_child_count` int(12) unsigned DEFAULT 0,
  PRIMARY KEY (`menu_id`) USING BTREE,
  KEY `IDX_parent` (`menu_parent_id`) USING BTREE,
  KEY `IDX_level` (`menu_level`) USING BTREE,
  KEY `IDX_status` (`menu_status`) USING BTREE,
  KEY `IDX_is_login` (`menu_is_login`) USING BTREE,
  KEY `IDX_code` (`menu_code`) USING BTREE,
  KEY `IDX_position` (`menu_position`) USING BTREE,
  KEY `IDX_url` (`menu_link`) USING BTREE,
  CONSTRAINT `tbl_fe_menu_ibfk_1` FOREIGN KEY (`menu_parent_id`) REFERENCES `tbl_fe_menu` (`menu_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=461 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for tbl_log_error_sendmail
-- ----------------------------
DROP TABLE IF EXISTS `tbl_log_error_sendmail`;
CREATE TABLE `tbl_log_error_sendmail` (
  `log_error_id` bigint(19) unsigned NOT NULL AUTO_INCREMENT,
  `log_error_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `log_error_content` text COLLATE utf8mb4_unicode_ci DEFAULT '',
  `log_error_time` int(12) DEFAULT 0,
  PRIMARY KEY (`log_error_id`) USING BTREE,
  FULLTEXT KEY `FT_keyword` (`log_error_content`,`log_error_email`)
) ENGINE=MyISAM AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for tbl_project
-- ----------------------------
DROP TABLE IF EXISTS `tbl_project`;
CREATE TABLE `tbl_project` (
  `pr_id` bigint(19) unsigned NOT NULL AUTO_INCREMENT,
  `pr_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `pr_name` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pr_description` text COLLATE utf8mb4_unicode_ci DEFAULT '',
  `pr_sv_id` bigint(19) unsigned DEFAULT NULL,
  `pr_sv_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `pr_prc_id` bigint(19) unsigned DEFAULT NULL,
  `pr_prc_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `pr_json_image` text COLLATE utf8mb4_unicode_ci DEFAULT '[]',
  `pr_location` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pr_assigned_to` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pr_create_time` int(12) unsigned DEFAULT 0,
  `pr_update_time` int(12) unsigned DEFAULT 0,
  `pr_publish_time` int(12) unsigned DEFAULT 0,
  `pr_create_by` bigint(19) unsigned DEFAULT NULL,
  `pr_edit_by` bigint(19) unsigned DEFAULT NULL,
  `pr_status` smallint(1) unsigned DEFAULT 0 COMMENT 'Trạng thái',
  `pr_state` enum('DONE','PROCESS','PENDING','CANCEL','FAIL') COLLATE utf8mb4_unicode_ci DEFAULT 'PENDING' COMMENT 'Tình trạng của dự án',
  `pr_meta_title` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `pr_meta_desc` text COLLATE utf8mb4_unicode_ci DEFAULT '',
  `pr_meta_keyword` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT '[]',
  `pr_share_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  PRIMARY KEY (`pr_id`) USING BTREE,
  KEY `IDX_prc` (`pr_prc_id`) USING BTREE,
  KEY `IDX_prc_code` (`pr_prc_code`) USING BTREE,
  KEY `IDX_sv` (`pr_sv_id`) USING BTREE,
  KEY `IDX_sv_code` (`pr_sv_code`) USING BTREE,
  KEY `IDX_state` (`pr_state`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for tbl_project_cate
-- ----------------------------
DROP TABLE IF EXISTS `tbl_project_cate`;
CREATE TABLE `tbl_project_cate` (
  `prc_id` bigint(19) unsigned NOT NULL AUTO_INCREMENT,
  `prc_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `prc_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prc_parent_id` bigint(19) unsigned DEFAULT NULL,
  `prc_image` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `prc_status` smallint(1) unsigned DEFAULT 1,
  `prc_created_by` bigint(19) unsigned DEFAULT NULL,
  `prc_edit_by` bigint(19) unsigned DEFAULT NULL,
  `prc_created_time` int(12) unsigned DEFAULT 0,
  `prc_updated_time` int(12) unsigned DEFAULT 0,
  `prc_meta_title` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `prc_meta_desc` text COLLATE utf8mb4_unicode_ci DEFAULT '',
  `prc_meta_keyword` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `prc_is_use` smallint(1) unsigned DEFAULT 0,
  PRIMARY KEY (`prc_id`) USING BTREE,
  KEY `IDX_parent_id` (`prc_parent_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for tbl_send_mail
-- ----------------------------
DROP TABLE IF EXISTS `tbl_send_mail`;
CREATE TABLE `tbl_send_mail` (
  `send_mail_id` bigint(19) unsigned NOT NULL AUTO_INCREMENT,
  `send_mail_account` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `send_mail_password` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `send_mail_total` int(11) unsigned DEFAULT 0,
  PRIMARY KEY (`send_mail_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for tbl_service
-- ----------------------------
DROP TABLE IF EXISTS `tbl_service`;
CREATE TABLE `tbl_service` (
  `sv_id` bigint(19) unsigned NOT NULL AUTO_INCREMENT,
  `sv_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sv_title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `sv_description` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sv_icon` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sv_edit_by` bigint(19) unsigned DEFAULT NULL,
  `sv_created_by` bigint(19) unsigned DEFAULT NULL,
  `sv_created_time` int(12) unsigned DEFAULT 0,
  `sv_updated_time` int(12) unsigned DEFAULT 0,
  `sv_status` smallint(1) unsigned DEFAULT 0,
  `sv_is_use` smallint(1) unsigned DEFAULT 0,
  PRIMARY KEY (`sv_id`) USING BTREE,
  KEY `IDX_status` (`sv_status`) USING BTREE,
  KEY `IDX_code` (`sv_code`) USING BTREE,
  FULLTEXT KEY `FTS_title` (`sv_title`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for tbl_session
-- ----------------------------
DROP TABLE IF EXISTS `tbl_session`;
CREATE TABLE `tbl_session` (
  `ss_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `ss_user_id` bigint(19) unsigned DEFAULT NULL,
  `ss_user_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `ss_area_type` enum('MANAGER','CUSTOMER') COLLATE utf8mb4_unicode_ci DEFAULT 'MANAGER',
  `ss_agent` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `ss_ip` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `ss_provider` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `ss_browser` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `ss_browser_ver` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `ss_device` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `ss_device_type` smallint(2) unsigned DEFAULT 14 COMMENT '0:desktop\n1:smartphone\n2:tablet\n3:feature phone\n4:console\n5:tv\n6:car browser\n7:smart display\n8:camera\n9:portable media player\n10:phablet\n11:smart speaker\n12:wearable\n13:peripheral\n14:unknown',
  `ss_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ss_os` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `ss_os_ver` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `ss_sr_info` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `ss_is_bot` smallint(1) unsigned DEFAULT 0,
  `ss_time` int(12) unsigned DEFAULT NULL,
  `ss_is_login` smallint(1) unsigned DEFAULT 0,
  PRIMARY KEY (`ss_id`) USING BTREE,
  KEY `IDX_user_id` (`ss_user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Function structure for fnc_DeleteProjectCate
-- ----------------------------
DROP FUNCTION IF EXISTS `fnc_DeleteProjectCate`;
delimiter ;;
CREATE FUNCTION `fnc_DeleteProjectCate`(prcId BIGINT (19) UNSIGNED)
 RETURNS bigint(19) unsigned
  MODIFIES SQL DATA 
BEGIN
	DECLARE parentId BIGINT(19) DEFAULT NULL;
	
	SELECT prc_parent_id
    INTO parentId
    FROM tbl_project_cate
    WHERE prc_id = prcId;

    IF parentId IS NULL THEN
        DELETE FROM tbl_project_cate WHERE prc_parent_id = prcId;
    END IF;

    DELETE FROM tbl_project_cate WHERE prc_id = prcId;
		
    RETURN prcId;
END
;;
delimiter ;

-- ----------------------------
-- Function structure for fnc_getMailConfigs
-- ----------------------------
DROP FUNCTION IF EXISTS `fnc_getMailConfigs`;
delimiter ;;
CREATE FUNCTION `fnc_getMailConfigs`()
 RETURNS text CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci
  MODIFIES SQL DATA 
BEGIN
	DECLARE svConfig TEXT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '{}';
	DECLARE mailId INT(11) DEFAULT NULL;
	DECLARE mailAcc,mailPW VARCHAR(200) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '';
	
	SELECT constant_content 
	INTO svConfig
	FROM tbl_constant WHERE constant_code='system_server_mail_config' LIMIT 1 OFFSET 0;
	
	IF svConfig IS NULL THEN
		SET svConfig = '{}';
	END IF;
	
	SELECT send_mail_id, send_mail_account, send_mail_password
	INTO mailId, mailAcc, mailPW
	FROM tbl_send_mail ORDER BY send_mail_total ASC LIMIT 1 OFFSET 0;
	
	IF mailId > 0 THEN
		UPDATE tbl_send_mail SET send_mail_total=send_mail_total+1 WHERE send_mail_id = mailId;
	ELSE 
		SET mailAcc = '';
		SET mailPW = '';
	END IF;
	
	SET svConfig = JSON_SET(svConfig, '$.username', mailAcc, '$.password', mailPW);
	RETURN svConfig;
END
;;
delimiter ;

-- ----------------------------
-- Function structure for fun_CreateDeviceInfo
-- ----------------------------
DROP FUNCTION IF EXISTS `fun_CreateDeviceInfo`;
delimiter ;;
CREATE FUNCTION `fun_CreateDeviceInfo`(jsonData LONGTEXT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci)
 RETURNS varchar(100) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci
  MODIFIES SQL DATA 
BEGIN
	DECLARE ssId, id VARCHAR(100) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;
	
	SET id = JSON_VALUE(jsonData, '$.id');
	
    SELECT ss_id
    INTO ssId
    FROM tbl_session
    WHERE ss_id = id;

    IF ssId IS NULL THEN
        INSERT INTO tbl_session(
            ss_id,
            ss_user_id,
            ss_user_code,
            ss_area_type,
            ss_agent,
            ss_ip,
            ss_provider,
            ss_browser,
            ss_browser_ver,
            ss_device,
            ss_device_type,
            ss_type,
            ss_os,
            ss_os_ver,
            ss_sr_info,
            ss_is_bot,
            ss_time,
            ss_is_login
        ) VALUES (
            id,
            JSON_VALUE(jsonData, '$.user_id'),
            IFNULL(JSON_VALUE(jsonData, '$.user_id'), ''),
            IFNULL(JSON_VALUE(jsonData, '$.area_type'), 'MANAGER'),
            IFNULL(JSON_VALUE(jsonData, '$.agent'), ''),
            IFNULL(JSON_VALUE(jsonData, '$.ip'), ''),
            IFNULL(JSON_VALUE(jsonData, '$.provider'), ''),
            IFNULL(JSON_VALUE(jsonData, '$.browser'), ''),
            IFNULL(JSON_VALUE(jsonData, '$.browser_ver'), ''),
            IFNULL(JSON_VALUE(jsonData, '$.device'), ''),
            IFNULL(JSON_VALUE(jsonData, '$.device_type'), 14),
            IFNULL(JSON_VALUE(jsonData, '$.type'), ''),
            IFNULL(JSON_VALUE(jsonData, '$.os'), ''),
            IFNULL(JSON_VALUE(jsonData, '$.os_ver'), ''),
            IFNULL(JSON_VALUE(jsonData, '$.sr_info'), ''),
            IFNULL(JSON_VALUE(jsonData, '$.is_bot'), 0),
            IFNULL(JSON_VALUE(jsonData, '$.time'), UNIX_TIMESTAMP()),
            IFNULL(JSON_VALUE(jsonData, '$.is_login'), 0)
        );
    ELSE
        UPDATE tbl_session SET
            ss_user_id = JSON_VALUE(jsonData, '$.user_id'),
            ss_user_code = IFNULL(JSON_VALUE(jsonData, '$.user_id'), ''),
            ss_area_type = IFNULL(JSON_VALUE(jsonData, '$.area_type'), 'MANAGER'),
            ss_agent = IFNULL(JSON_VALUE(jsonData, '$.agent'), ''),
            ss_ip = IFNULL(JSON_VALUE(jsonData, '$.ip'), ''),
            ss_provider = IFNULL(JSON_VALUE(jsonData, '$.provider'), ''),
            ss_browser = IFNULL(JSON_VALUE(jsonData, '$.browser'), ''),
            ss_browser_ver = IFNULL(JSON_VALUE(jsonData, '$.browser_ver'), ''),
            ss_device = IFNULL(JSON_VALUE(jsonData, '$.device'), ''),
            ss_device_type = IFNULL(JSON_VALUE(jsonData, '$.device_type'), 14),
            ss_type = IFNULL(JSON_VALUE(jsonData, '$.type'), ''),
            ss_os = IFNULL(JSON_VALUE(jsonData, '$.os'), ''),
            ss_os_ver = IFNULL(JSON_VALUE(jsonData, '$.os_ver'), ''),
            ss_sr_info = IFNULL(JSON_VALUE(jsonData, '$.sr_info'), ''),
            ss_is_bot = IFNULL(JSON_VALUE(jsonData, '$.is_bot'), 0),
            ss_time = IFNULL(JSON_VALUE(jsonData, '$.time'), UNIX_TIMESTAMP()),
            ss_is_login = IFNULL(JSON_VALUE(jsonData, '$.is_login'), 0)
        WHERE ss_id = id;
    END IF;
		
    RETURN id;
END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for sp_ChangeStatusProjectCate
-- ----------------------------
DROP PROCEDURE IF EXISTS `sp_ChangeStatusProjectCate`;
delimiter ;;
CREATE PROCEDURE `sp_ChangeStatusProjectCate`(IN prcId INT(11),
    IN prcStatus SMALLINT(1))
  MODIFIES SQL DATA 
BEGIN
	UPDATE tbl_project_cate 
	SET prc_status = prcStatus
	WHERE prc_id = prcId;
	
	IF prcStatus = 0 THEN
	    UPDATE tbl_project_cate 
	    SET prc_status = prcStatus
	    WHERE prc_parent_id = prcId;
	END IF;
END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for sp_DeleteMultipleProjectCate
-- ----------------------------
DROP PROCEDURE IF EXISTS `sp_DeleteMultipleProjectCate`;
delimiter ;;
CREATE PROCEDURE `sp_DeleteMultipleProjectCate`(IN prcIds TEXT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci)
  MODIFIES SQL DATA 
BEGIN
	DECLARE total, idx, id BIGINT(19) UNSIGNED DEFAULT 0;
	SET total = JSON_LENGTH(prcIds);

	WHILE idx < total DO
		SET id = JSON_VALUE(prcIds, CONCAT('$[', idx, ']'));
		SELECT fnc_DeleteProjectCate(id);
		SET idx = idx + 1;
	END WHILE;
END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for sp_DelManagerUserById
-- ----------------------------
DROP PROCEDURE IF EXISTS `sp_DelManagerUserById`;
delimiter ;;
CREATE PROCEDURE `sp_DelManagerUserById`(IN uId INT(19))
  MODIFIES SQL DATA 
BEGIN
	DELETE FROM tbl_admin_fts
	WHERE afts_adm_id = uId;
	
	DELETE FROM tbl_admin
	WHERE adm_id = uId;
	
	DELETE FROM tbl_client
	WHERE client_user_id = uId
	AND client_area_type = 'MANAGER';
END
;;
delimiter ;

SET FOREIGN_KEY_CHECKS = 1;
