<?php
namespace Models\Entities\Generated;

use Models\Entities\Abstracted\Status;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;

/**
 * @MappedSuperclass
 */
abstract class Admin extends Status
{
    /**
     * @Id
     * @Column(type="bigint", length=19, nullable=false)
     * @GeneratedValue(strategy="AUTO")
     */
    protected $adm_id;
    /**
     * @Column(type="string", length=25, nullable=false)
     */
    protected $adm_code = '';
    /**
     * @Column(type="string", length=255, nullable=false)
     */
    protected $adm_bg_timeline = '';
    /**
     * @Column(type="string", length=100, nullable=false)
     */
    protected $adm_username = '';
    /**
     * @Column(type="string", length=100, nullable=false)
     */
    protected $adm_password = '';
    /**
     * @Column(type="smallint", length=1, nullable=false)
     */
    protected $adm_status = self::STATUS_UNACTIVE;
    /**
     * @Column(type="string", length=10, nullable=false)
     */
    protected $adm_groupcode = 'STAFF';
    /**
     * @Column(type="string", length=255, nullable=false)
     */
    protected $adm_fullname = '';
    /**
     * @Column(type="string", length=255, nullable=false)
     */
    protected $adm_avatar = '';
    /**
     * @Column(type="string", length=255, nullable=false)
     */
    protected $adm_address = '';
    /**
     * @Column(type="string", length=150, nullable=false)
     */
    protected $adm_email = '';
    /**
     * @Column(type="string", length=20, nullable=false)
     */
    protected $adm_phone = '';
    /**
     * @Column(type="string", length=10, nullable=false)
     */
    protected $adm_phone_code = '';
    /**
     * @Column(type="integer", length=12, nullable=false)
     */
    protected $adm_create_time = 0;
    /**
     * @Column(type="integer", length=12, nullable=false)
     */
    protected $adm_update_time = 0;
    /**
     * @Column(type="integer", length=12, nullable=false)
     */
    protected $adm_last_login_time = 0;
    /**
     * @Column(type="string", length=100, nullable=false)
     */
    protected $adm_ssid = '';
    /**
     * @Column(type="integer", length=12, nullable=false)
     */
    protected $adm_last_access_time = 0;
    /**
     * @Column(type="integer", length=12, nullable=false)
     */
    protected $adm_first_login_time = 0;
    /**
     * @Column(type="smallint", length=1, nullable=false)
     */
    protected $adm_is_del = 0;
    /**
     * @Column(type="integer", length=12, nullable=false)
     */
    protected $adm_blocked_time = 0;
    /**
     * @Column(type="string", length=10, nullable=false)
     */
    protected $adm_gender = 'MALE';
    /**
     * @Column(type="integer", length=12, nullable=false)
     */
    protected $adm_birthday = 0;
     /**
     * @Column(type="string", length=255, nullable=false)
     */
    protected $adm_json_name = '{"first":"","last":""}';
}