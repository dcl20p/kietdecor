CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ChangeStatusProjectCate`(
    IN prcId INT(11),
    IN prcStatus SMALLINT(1)
)
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