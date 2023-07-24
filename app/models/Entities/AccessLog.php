<?php
namespace Models\Entities;

use \Models\Entities\Generated;
use \Doctrine\ORM\Mapping\Entity;
use \Doctrine\ORM\Mapping\Table;

/**
 * @Entity(repositoryClass="\Models\Repositories\AccessLog")
 * @Table(name="tbl_access_log")
 */
class AccessLog extends Generated\AccessLog
{
    const SITE_MANAGERS = 'MANAGERS';
    const SITE_CUSTOMER = 'CUSTOMER';
    /**
     * Set access log params
     * @param array $data
     * @return \Models\Entities\AccessLog
     */
    public function setAl_params(array $data = [])
    {
        $this->al_params = json_encode(new \ArrayObject($data));
        if (empty($this->al_params))
            $this->al_params = '{}';
        return $this;
    }

    /**
     * get access log params
     * @return array
     */
    public function getAl_params()
    {
        if ($this->al_params && $this->al_params != '{}') {
            return json_decode($this->al_params, true);
        }
        return [];
    }
}
?>