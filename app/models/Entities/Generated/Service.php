<?php
namespace Models\Entities\Generated;

use \Models\Entities\Abstracted\Status;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;

/**
 * @MappedSuperclass
 */
abstract class Service extends Status
{
    /**
     * @Id
     * @Column(type="bigint", length=19, nullable=false)
     * @GeneratedValue(strategy="AUTO")
     */
    protected $sv_id;
    /**
     * @Column(type="string", length=20, nullable=false)
     */
    protected $sv_code = '';
    /**
     * @Column(type="string", length=100, nullable=false)
     */
    protected $sv_title = '';
    /**
     * @Column(type="string", length=2048, nullable=false)
     */
    protected $sv_description = '';
    /**
     * @Column(type="string", length=100, nullable=false)
     */
    protected $sv_icon = '';
    /**
     * @Column(type="bigint", length=19, nullable=false)
     */
    protected $sv_edit_by;
    /**
     * @Column(type="bigint", length=19, nullable=false)
     */
    protected $sv_created_by;
    /**
     * @Column(type="smallint", length=1, nullable=false)
     */
    protected $sv_status = self::STATUS_UNACTIVE;
    /**
     * @Column(type="integer", length=12, nullable=false)
     */
    protected $sv_created_time = 0;
    /**
     * @Column(type="integer", length=12, nullable=false)
     */
    protected $sv_updated_time = 0;
    /**
     * @Column(type="smallint", length=1, nullable=false)
     */
    protected $sv_is_use = self::STATUS_UNACTIVE;
}