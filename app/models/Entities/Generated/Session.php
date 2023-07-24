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
abstract class Session extends Status
{
    /**
     * @Id
     * @Column(type="string", length=100, nullable=false)
     */
    protected $ss_id;
    /**
     * @Column(type="bigint", length=19, nullable=false)
     */
    protected $ss_user_id;
    /**
     * @Column(type="string", length=20, nullable=false)
     */
    protected $ss_user_code = '';
    /**
     * @Column(type="string", length=20, nullable=false)
     */
    protected $ss_area_type = 'MANAGER';
    /**
     * @Column(type="string", length=512, nullable=false)
     */
    protected $ss_agent = '';
    /**
     * @Column(type="string", length=100, nullable=false)
     */
    protected $ss_ip = '';
    /**
     * @Column(type="string", length=255, nullable=false)
     */
    protected $ss_provider = '';
    /**
     * @Column(type="string", length=100, nullable=false)
     */
    protected $ss_browser = '';
    /**
     * @Column(type="string", length=100, nullable=false)
     */
    protected $ss_browser_ver = '';
    /**
     * @Column(type="string", length=100, nullable=false)
     */
    protected $ss_type = '';
    /**
     * @Column(type="string", length=100, nullable=false)
     */
    protected $ss_device = '';
    /**
     * @Column(type="smallint", length=2, nullable=false)
     */
    protected $ss_device_type = 14;
    /**
     * @Column(type="string", length=100, nullable=false)
     */
    protected $ss_os = '';
    /**
     * @Column(type="string", length=100, nullable=false)
     */
    protected $ss_os_ver = '';
    /**
     * @Column(type="string", length=100, nullable=false)
     */
    protected $ss_sr_info = '';
    /**
     * @Column(type="smallint", length=1, nullable=false)
     */
    protected $ss_is_bot = 0;
    /**
     * @Column(type="integer", length=12, nullable=false)
     */
    protected $ss_time = 0;
    /**
     * @Column(type="smallint", length=1, nullable=false)
     */
    protected $ss_is_login = 0;
}