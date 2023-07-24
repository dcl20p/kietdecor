<?php
namespace Models\Entities\Abstracted;

use Zf\Ext\Model\ZFModelEntity;

/**
 * Abstract class for status entities.
 */
abstract class Status extends ZFModelEntity
{
    /**
     * State unactive
     * @var string
     */
    const STATUS_UNACTIVE = 0;
    
    /**
     * State active
     * @var string
     */
    const STATUS_ACTIVE = 1;

    /**
     * Returns an array of status codes and their corresponding labels.
     *
     * @return array<int, string>
     */
    public static function returnStatus(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_UNACTIVE => 'Unactive',
        ];
    }

}