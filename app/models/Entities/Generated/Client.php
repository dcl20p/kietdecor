<?php

namespace Models\Entities\Generated;

use \Models\Entities\Abstracted\Status;
use \Doctrine\ORM\Mapping\MappedSuperclass;
use \Doctrine\ORM\Mapping\Id;
use \Doctrine\ORM\Mapping\Column;
use \Doctrine\ORM\Mapping\GeneratedValue;
/**
 * @MappedSuperclass
 */
abstract class Client extends Status
{
    /**
     * @Id
     * @Column(type="bigint", length=19, nullable=false)
     * @GeneratedValue(strategy="AUTO")
     */
    protected $client_id;
    /**
     * @Column(type="bigint", length=19, nullable=false)
     */
    protected $client_user_id;
    /**
     * @Column(type="string", length=20, nullable=false)
     */
    protected $client_user_code = '';
    /**
     * @Column(type="string", length=20, nullable=false)
     */
    protected $client_area_type = 'CUSTOMER';
    /**
     * @Column(type="string", length=512, nullable=false)
     */
    protected $client_endpoint = '';
    /**
     * @Column(type="string", length=255, nullable=false)
     */
    protected $client_auth_token = '';
    /**
     * @Column(type="string", length=255, nullable=false)
     */
    protected $client_public_key = '';
    /**
     * @Column(type="string", length=15, nullable=false)
     */
    protected $client_type = 'CHROME';
    /**
     * @Column(type="string", length=255, nullable=false)
     */
    protected $client_package = '';
    /**
     * @Column(type="integer", length=12, nullable=false)
     */
    protected $client_join_time = 0;
    /**
     * @Column(type="string", length=255, nullable=false)
     */
    protected $client_device_id = '';
}