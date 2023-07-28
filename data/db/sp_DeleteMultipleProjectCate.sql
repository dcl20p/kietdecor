CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_DeleteMultipleProjectCate`(
    IN prcIds TEXT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci
)
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