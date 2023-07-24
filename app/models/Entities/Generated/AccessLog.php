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
abstract class AccessLog extends Status
{
    /**
     * @Id
     * @Column(type="bigint", length=19, nullable=false)
     * @GeneratedValue(strategy="AUTO")
     */
    protected $al_id;
    /**
     * @Column(type="bigint", length=19, nullable=false)
     */
    protected $al_user_id;
    /**
     * @Column(type="string", length=20, nullable=false)
     */
    protected $al_user_code = '';
    /**
     * @Column(type="string", length=20, nullable=false)
     */
    protected $al_site = 'CUSTOMER';
    /**
     * @Column(type="string", length=255, nullable=false)
     */
    protected $al_url = '';
    /**
     * @Column(type="string", length=20, nullable=false)
     */
    protected $al_method = '';
    /**
     * @Column(type="string", length=50, nullable=false)
     */
    protected $al_route_name = '';
    /**
     * @Column(type="string", length=60000, nullable=false)
     */
    protected $al_params = '';
    /**
     * @Column(type="string", length=255, nullable=false)
     */
    protected $al_ip_address = '';
    /**
     * @Column(type="integer", length=12, nullable=false)
     */
    protected $al_created = 0;
    /**
     * @Column(type="string", length=255, nullable=false)
     */
    protected $al_state = '';
    /**
     * @Column(type="string", length=255, nullable=false)
     */
    protected $al_content = '';
}