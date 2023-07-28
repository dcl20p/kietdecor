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
abstract class ProjectCate extends Status
{
    /**
     * @Id
     * @Column(type="bigint", length=19, nullable=false)
     * @GeneratedValue(strategy="AUTO")
     */
    protected $prc_id;
    /**
     * @Column(type="string", length=20, nullable=false)
     */
    protected $prc_code = '';
    /**
     * @Column(type="string", length=100, nullable=false)
     */
    protected $prc_name = '';
    /**
     * @Column(type="string", length=100, nullable=false)
     */
    protected $prc_image = '';
    /**
     * @Column(type="bigint", length=19, nullable=false)
     */
    protected $prc_parent_id;
    /**
     * @Column(type="bigint", length=19, nullable=false)
     */
    protected $prc_edit_by;
    /**
     * @Column(type="bigint", length=19, nullable=false)
     */
    protected $prc_created_by;
    /**
     * @Column(type="smallint", length=1, nullable=false)
     */
    protected $prc_status = self::STATUS_UNACTIVE;
    /**
     * @Column(type="smallint", length=1, nullable=false)
     */
    protected $prc_is_use = self::STATUS_UNACTIVE;
    /**
     * @Column(type="integer", length=12, nullable=false)
     */
    protected $prc_created_time = 0;
    /**
     * @Column(type="integer", length=12, nullable=false)
     */
    protected $prc_updated_time = 0;
    /**
     * @Column(type="string", length=1024, nullable=false)
     */
    protected $prc_meta_title = '';
    /**
     * @Column(type="string", nullable=false)
     */
    protected $prc_meta_desc = '';
    /**
     * @Column(type="string", length=2048, nullable=false)
     */
    protected $prc_meta_keyword = '';
}