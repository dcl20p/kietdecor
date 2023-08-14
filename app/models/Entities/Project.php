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

    /**
     * Set image
     * @param array $data
     * @return self
     */
    public function setPr_json_image(array $data = [])
    {
        $json = '[]';
        if ( !empty($data) ){
            $json = @json_encode(array_values($data)) ?? '[]';
        }
        
        $this->pr_json_image = $json;
        return $this;
    }
    
    /**
     * Get image
     * @return array
     */
    public function getPr_json_image(): array
    {
        return @json_decode(
            $this->pr_json_image, true
        ) ?? [];
    }
}

?>