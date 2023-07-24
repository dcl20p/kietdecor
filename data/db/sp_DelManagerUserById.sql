CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_DelManagerUserById`(
     IN uId INT(19)
)
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