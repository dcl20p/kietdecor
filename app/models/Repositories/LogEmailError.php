<?php
namespace Models\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class LogEmailError extends EntityRepository
{
    /**
     *
     * @param QueryBuilder $qb
     * @param string $val
     * @return QueryBuilder
     */
    protected function _filter_keyword(QueryBuilder $qb, string $val): QueryBuilder
    {
        if ($val == '')
            return $qb;
        return $qb
            ->andWhere('MATCH(LEE.log_error_email, LEE.log_error_content) AGAINST (:kw BOOLEAN) > 0')
            ->setParameter('kw', $val);
    }

    /**
     * Listing
     *
     * @param array $opts
     * @return mixed
     */
    public function fetchOpts(array|QueryBuilder $opts = []): mixed
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('LEE')
            ->from($this->getEntityName(), 'LEE');

        // Filter
        if (isset($opts['params']) && is_array($opts['params'])) {
            foreach ($opts['params'] as $key => $val) {
                $this->{"_filter_{$key}"}($qb, $val);
            }
        }

        // Order
        if (isset($opts['order']) && is_array($opts['order'])) {
            foreach ($opts['order'] as $col => $mode)
                $qb->addOrderBy("LEE.error_{$col}", $mode);
        } else
            $qb->addOrderBy('LEE.log_error_time', 'DESC');

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