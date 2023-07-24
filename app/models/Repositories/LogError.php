<?php

namespace Models\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class LogError extends EntityRepository
{
    /**
     * @param \Doctrine\ORM\QueryBuilder $qb
     * @param integer $val
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function _filter_user_id(QueryBuilder $qb, int $val): QueryBuilder
    {
        return $qb->andWhere('LE.error_user_id = :user_id')
            ->setParameter('user_id', $val);
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb
     * @param string $val
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function _filter_uri(QueryBuilder $qb, string $val): QueryBuilder
    {
        if (is_array($val))
            return $qb->andWhere('LE.token_uri IN(:uris)')
                ->setParameter('uris', $val);

        return $qb->andWhere('LE.token_uri = :uri')
            ->setParameter('uri', $val);
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb
     * @param string $val
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function _filter_method(QueryBuilder $qb, string $val): QueryBuilder
    {
        if (is_array($val))
            return $qb->andWhere('LE.error_method IN(:methods)')
                ->setParameter('methods', $val);

        return $qb->andWhere('LE.error_method = :method')
            ->setParameter('method', $val);
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb
     * @param string $val
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function _filter_code(QueryBuilder $qb, string $val): QueryBuilder
    {
        if (is_array($val))
            return $qb->andWhere('LE.error_code IN(:codes)')
                ->setParameter('codes', $val);

        return $qb->andWhere('LE.error_code = :code')
            ->setParameter('code', $val);
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb
     * @param smallint $val
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function _filter_status(QueryBuilder $qb, int $val): QueryBuilder
    {
        if (empty($val)) return $qb;
        return $qb->andWhere('LE.error_status = :status')
            ->setParameter('status', $val);
    }
    /**
     * @param \Doctrine\ORM\QueryBuilder $qb
     * @param string $val
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function _filter_keyword(QueryBuilder $qb, string $val): QueryBuilder
    {
        if ($val == '') return $qb;
        return $qb
        ->andWhere('MATCH(LE.error_uri, LE.error_params, LE.error_msg, LE.error_trace) AGAINST (:kw BOOLEAN) > 0')
        ->setParameter('kw', $val);
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb
     * @param integer $val
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function _filter_limit(QueryBuilder $qb, int $val): QueryBuilder
    {
        return $qb->setMaxResults($val)
            ->setFirstResult(0);
    }

    /**
     * @param array $params
     * @return array|\Doctrine\ORM\QueryBuilder
     * @return mixed
     */
    public function fetchOpts(array|QueryBuilder $opts = []): mixed
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('LE')
            ->from($this->getEntityName(), 'LE');

        // Filter
        if (isset($opts['params']) && is_array($opts['params'])) {
            foreach ($opts['params'] as $key => $val) {
                $this->{"_filter_{$key}"}($qb, $val);
            }
        }

        // Order
        if (isset($opts['order']) && is_array($opts['order'])) {
            foreach ($opts['order'] as $col => $mode)
                $qb->addOrderBy("LE.error_{$col}", $mode);
        } else
            $qb->addOrderBy('LE.error_time', 'DESC');

        // dd($qb->getParameters(), $qb->getQuery()->getSQL());

        // -- Result
        switch ($opts['resultMode'] ?? '') {
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
    }
}
