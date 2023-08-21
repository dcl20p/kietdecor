<?php
namespace Models\Entities;

use \Models\Entities\Generated;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

/**
 * @Entity(repositoryClass="Models\Repositories\ProjectCate")
 * @Table(name="tbl_project_cate")
 */
class ProjectCate extends Generated\ProjectCate
{
    const FOLDER_TOKEN = 'project_cate';
    const PROJECT_CATE_IMAGE_SIZES = [
        '1' => '200x200',
    ];
}

?>