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
abstract class LogEmailError extends Status
{
    /**
     * @Id
     * @Column(type="bigint", length=19, nullable=false)
     * @GeneratedValue(strategy="AUTO")
     */
    protected $log_error_id;
    /**
     * @Column(type="string", length=255, nullable=false)
     */
    protected $log_error_email;
    /**
     * @Column(type="string", nullable=false)
     */
    protected $log_error_content = '';
    /**
     * @Column(type="integer", length=12, nullable=false)
     */
    protected $log_error_time = 0;

}