CREATE DEFINER=`root`@`localhost` FUNCTION `fun_CreateDeviceInfo`(jsonData LONGTEXT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci
) RETURNS varchar(100) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci
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