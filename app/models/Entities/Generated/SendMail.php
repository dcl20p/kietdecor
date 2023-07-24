<?php
namespace Models\Entities\Generated;

use Models\Entities\Abstracted;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;

/**
 * @MappedSuperclass
 */
abstract class SendMail extends Abstracted\Status
{
    /**
     * @Id
     * @Column(type="bigint", length=19, nullable=false)
     * @GeneratedValue(strategy="AUTO")
     */
    protected $send_mail_id;
    /**
     * @Column(type="string", length=150, nullable=false)
     */
    protected $send_mail_account = '';
    /**
     * @Column(type="string", length=100, nullable=false)
     */
    protected $send_mail_password = '';
    /**
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $send_mail_total = 0;
}

?>