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
abstract class Project extends Status
{
    /**
     * @Id
     * @Column(type="bigint", length=19, nullable=false)
     * @GeneratedValue(strategy="AUTO")
     */
    protected $pr_id;
    /**
     * @Column(type="string", length=20, nullable=false)
     */
    protected $pr_code = '';
    /**
     * @Column(type="string", length=512, nullable=false)
     */
    protected $pr_name = '';
    /**
     * @Column(type="string", nullable=false)
     */
    protected $pr_description = '';
    /**
     * @Column(type="bigint", length=19, nullable=false)
     */
    protected $pr_sv_id;
    /**
     * @Column(type="string", length=20, nullable=false)
     */
    protected $pr_sv_code = '';
    /**
     * @Column(type="bigint", length=19, nullable=false)
     */
    protected $pr_prc_id;
    /**
     * @Column(type="string", length=20, nullable=false)
     */
    protected $pr_prc_code;
    /**
     * @Column(type="string", nullable=false)
     */
    protected $pr_json_image = '[]';
    /**
     * @Column(type="string", length=255, nullable=false)
     */
    protected $pr_location = '';
    /**
     * @Column(type="string", length=50, nullable=false)
     */
    protected $pr_assigned_to = '';
    /**
     * @Column(type="bigint", length=19, nullable=false)
     */
    protected $pr_edit_by;
    /**
     * @Column(type="bigint", length=19, nullable=false)
     */
    protected $pr_create_by;
    /**
     * @Column(type="smallint", length=1, nullable=false)
     */
    protected $pr_status = self::STATUS_UNACTIVE;
    /**
     * @Column(type="integer", length=12, nullable=false)
     */
    protected $pr_create_time = 0;
    /**
     * @Column(type="integer", length=12, nullable=false)
     */
    protected $pr_update_time = 0;
    /**
     * @Column(type="integer", length=12, nullable=false)
     */
    protected $pr_publish_time = 0;
    /**
     * @Column(type="string", length=20, nullable=false)
     */
    protected $pr_state = '[]';
    /**
     * @Column(type="string", length=1024, nullable=false)
     */
    protected $pr_meta_title = '';
    /**
     * @Column(type="string", nullable=false)
     */
    protected $pr_meta_desc = '';
    /**
     * @Column(type="string", length=2048, nullable=false)
     */
    protected $pr_meta_keyword = '[]';
}