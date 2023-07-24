<?php 
namespace Models\Entities\Generated;
use Models\Entities\Abstracted\Status;
use \Doctrine\ORM\Mapping\MappedSuperclass;
use \Doctrine\ORM\Mapping\Id;
use \Doctrine\ORM\Mapping\Column;

/**
 * @MappedSuperclass
 */
abstract class AdminFts extends Status
{
    /**
	 * @Id
	 * @Column(type="bigint", length=19, nullable=false)
	 */
    protected $afts_adm_id;
    /**
	 * @Column(type="string", length=20, nullable=false)
	 */
    protected $afts_adm_code='';
    /**
     * @Column(type="string", length=1000, nullable=false)
     */
    protected $afts_kw='';
}