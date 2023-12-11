CREATE DEFINER=`root`@`localhost` 
FUNCTION `fnc_DeleteProjectCate`(
    prcId BIGINT (19) UNSIGNED
) RETURNS BIGINT(19) UNSIGNED
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