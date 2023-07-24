<?php
namespace Models\Repositories\Abstracted;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Zf\Ext\Model\ZFDtPaginator;

abstract class Repository extends EntityRepository
{
    /**
     * Convert bigint
     * @param string $val
     * @param integer $maxLength
     * @return number
     */
    protected function getBigintVal(string $val, int $maxLenth = 19): int
    {
        $value = ltrim(trim($val), '0');
        if (is_numeric($value) && is_int($value)) {
            return $value;
        }

        $maths = [];
        preg_match('/^([0-9]{1,' . $maxLenth . '}).*/', $value, $maths);
        if (isset($maths[1]))
            return $maths[1];

        return 0;
    }

    /**
     * Get normal string
     * @param number $val
     * @return mixed
     */
    protected function getIdStr(string $val): mixed
    {
        return preg_replace('/[^a-z0-9]/i', '', $val);
    }

    /**
     * Check and get only normal string item of array
     * @param array $arr
     * @return array if success. false if no any item of array is integer.
     */
    protected function validArrayIdStr(array $arr): array
    {
        $rsArr = [];
        foreach ($arr as $item) {
            $parse = $this->getIdStr($item);
            if ($parse == $item) {
                $rsArr[] = $parse;
            }
        }
        return $rsArr;
    }

     /**
     * Get only numeric value
     * @param integer $val
     * @param string $zeroStart Default is no any zero
     * @return string
     */
    protected function getNumericVal(int $val = 0, string $zeroStart = '^0+|'): string
    {
        return preg_replace('/(' . $zeroStart . '[^0-9\.])/m', '', $val);
    }

    /**
     * Check and get only integer item of array
     * @param array $arr
     * @return array if success. false if no any item of array is integer.
     */
    protected function validArrayInt(array $arr): array
    {
        $rsArr = [];
        foreach ($arr as $item) {
            $intVal = intval($item);
            if ($intVal == $item) {
                $rsArr[] = $intVal;
            }
        }
        return $rsArr;
    }

    /**
     * Check and get only numeric item of array
     * @param array $arr
     * @return array
     */
    protected function validArrayNumeric(array $arr): array
    {
        $rsArr = [];
        foreach ($arr as $item) {
            if (is_numeric($item)) {
                $rsArr[] = $this->getNumericVal($item);
            }
        }
        return $rsArr;
    }
    
    /**
     * Check and get only bigint item of array
     * @param array $arr
     * @return array
     */
    protected function validArrayBigint(array $arr): array
    {
        $rsArr = [];
        foreach ($arr as $item) {
            if ( !empty($item = $this->getBigintVal($item)) ) {
                $rsArr[] = $item;
            }
        }
        
        return $rsArr;
    }

    /**
     * 
     * @param QueryBuilder $qb
     * @param array $opts
     * @param string $prefix
     * @param string $dMode
     * @return void
     */
    protected function makeOderByOpts(QueryBuilder $qb, array $opts, string $prefix, string $dMode = 'DESC'): void
    {
        if (isset($opts['order'])
            && is_array($opts['order'])
        ) {
            foreach ($opts['order'] as $col => $mode) {
                $qb->addOrderBy("{$prefix}_{$col}", $mode);
            }
        } else
            $qb->addOrderBy("{$prefix}_id", $dMode);
    }

    /**
     *
     * @param QueryBuilder $qb
     * @param array $opts
     * @return void
     */
    protected function makeFilterParams(QueryBuilder $qb, array $opts): void
    {
        if (isset($opts['params'])
            && is_array($opts['params'])
        ) { 
            foreach ($opts['params'] as $key => $value) {
                if (false === $this->{"_filter_{$key}"}($qb, $value)) {
                    $qb->where('1:=zero')
                    ->setParameters(['zero' => 0])
                    ->setMaxResults(0)->setFirstResult(0);
                    break;
                }
            }
        }
    }

    /**
     *
     * @param QueryBuilder $qb
     * @param string $mode
     * @param boolean $fetchJoinKey
     * @return array|Query
     */
    protected function switchResponse(QueryBuilder $qb, string $mode, bool $fetchJoinKey = true): mixed
    {
        // Optimize pagination
        if ($fetchJoinKey === false) $qb->setParameter(
            ZFDtPaginator::FETCH_JOIN_COLLECTION, false
        );

        // Result
        switch ($mode) {
            case 'Array':
                return $qb->getQuery()->getArrayResult();
                break;
            case 'Entity':
                return $qb->getQuery()->getResult();
                break;
            case 'Query':
                return $qb->getQuery();
                break;
            case 'QueryBuilder':
            default:
                return $qb;
                break;
        }

        return $qb;
    }

    /**
     * Common fetchOpts
     *
     * @param string $class
     * @param string $alias
     * @param string $prefix
     * @param array $opts
     * @param boolean $fetchJoinKey
     * @return array|Query
     */
    protected function buildFetchOpts(string $class, string $alias, string $prefix, array $opts, bool $fetchJoinKey = true): mixed
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select($alias)->from($class, $alias);

        // Filter
        $this->makeFilterParams($qb, $opts);

        // Order
        $this->makeOderByOpts($qb, $opts, "{$alias}.{$prefix}");

        // dd($qb->getQuery()->getSQL(), $qb->getQuery()->getParameters());

        // Result
        return $this->switchResponse(
            $qb, $opts['resultMode'] ?? '',
            $fetchJoinKey
        );
    }
}