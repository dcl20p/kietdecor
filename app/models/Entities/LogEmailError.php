<?php
namespace Models\Entities;

use \Models\Entities\Generated;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

/**
 * @Entity(repositoryClass="\Models\Repositories\LogEmailError")
 * @Table(name="tbl_log_error_sendmail")
 */
class LogEmailError extends Generated\LogEmailError
{
    const LOG_ERROR_MAIL_FOLDER_TOKEN = 'log_error_mail';
}

?>