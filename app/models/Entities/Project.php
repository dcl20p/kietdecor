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
    const FOLDER_TOKEN = 'project';

    const PROJECT_THUMBNAIL_SIZES = [
        '1' => '305x196', 
        '2' => '324x208', 
        '4' => '393x253'
    ];

    const PROJECT_LIST_IMAGE_SIZES = [
        '1' => '297x182', 
        '2' => '222x0', 
        '4' => '413x0'
    ];

    const FOLDER_IMAGE = 'project';

}

?>