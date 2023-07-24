<?php
namespace Models\Entities;

use \Models\Entities\Generated;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

/**
 * @Entity(repositoryClass="\Models\Repositories\LogError")
 * @Table(name="tbl_error")
 */
class LogError extends Generated\LogError
{
    const LOG_ERROR_FOLDER_TOKEN = 'log_error';
}

?>