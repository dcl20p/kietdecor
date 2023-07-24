<?php
namespace Models\Entities;

use \Models\Entities\Generated;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

/**
 * @Entity(repositoryClass="Models\Repositories\Service")
 * @Table(name="tbl_service")
 */
class Service extends Generated\Service
{
}

?>