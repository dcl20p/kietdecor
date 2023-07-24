CREATE DEFINER=`root`@`localhost` FUNCTION `fnc_getMailConfigs`() RETURNS TEXT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci
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