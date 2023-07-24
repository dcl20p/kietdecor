<?php

namespace Models\Entities;

use \Models\Entities\Generated;
use \Doctrine\ORM\Mapping\Entity;
use \Doctrine\ORM\Mapping\Table;
/**
 * @Entity(repositoryClass="\Models\Repositories\Client")
 * @Table(name="tbl_client")
 */
class Client extends Generated\Client
{
    const TYPE_CHROME = 'CHROME';
    const TYPE_FIREFOX = 'FIREFOX';
    const TYPE_ANDROID = 'ANDROID';
    const TYPE_IOS = 'IOS';
    
    public static function returnTypes()
    {
        return [
            self::TYPE_CHROME => self::TYPE_CHROME,
            self::TYPE_FIREFOX => self::TYPE_FIREFOX,
            self::TYPE_ANDROID => self::TYPE_ANDROID,
            self::TYPE_IOS => self::TYPE_IOS
        ];
    }
    
    const AREA_CUSTOMER = 'CUSTOMER';
    const AREA_MANAGER = 'MANAGER';
}

?>