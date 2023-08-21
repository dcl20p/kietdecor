<?php
namespace Models\Entities;

use \Models\Entities\Generated;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

/**
 * @Entity(repositoryClass="Models\Repositories\Project")
 * @Table(name="tbl_project")
 */
class Project extends Generated\Project
{
    const PROJECT_IMAGE_SIZES = [
        '1' => '200x400', 
        '2' => '400x900', 
        '4' => '1000x300'
    ];
}

?>