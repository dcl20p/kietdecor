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
abstract class LogError extends Status
{
    /**
     * @Id
     * @Column(type="bigint", length=19, nullable=false)
     * @GeneratedValue(strategy="AUTO")
     */
    protected $error_id;
    /**
     * @Column(type="bigint", length=19, nullable=false)
     */
    protected $error_user_id;
    /**
     * @Column(type="string", length=255, nullable=false)
     */
    protected $error_uri = '';
    /**
     * @Column(type="string", nullable=false)
     */
    protected $error_params = '{}';
    /**
     * @Column(type="string", length=255, nullable=false)
     */
    protected $error_method = 'GET';
    /**
     * @Column(type="string", length=100, nullable=false)
     */
    protected $error_code = '';
    /**
     * @Column(type="string", nullable=false)
     */
    protected $error_msg = '';
    /**
     * @Column(type="string", nullable=false)
     */
    protected $error_trace = '';
    /**
     * @Column(type="smallint", length=1, nullable=false)
     */
    protected $error_status = 0;
    /**
     * @Column(type="integer", length=12, nullable=false)
     */
    protected $error_time = 0;
}