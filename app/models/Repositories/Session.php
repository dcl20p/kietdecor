<?php
namespace Models\Repositories;

use Doctrine\ORM\EntityRepository;
use Models\Entities\Session as EntitiesSession;
use Doctrine\ORM\QueryBuilder;

class Session extends EntityRepository
{
    /**
     * @param QueryBuilder $qb
     * @param string|array $val
     * @return QueryBuilder
     */
    protected function _filter_id(QueryBuilder $qb, mixed $val): QueryBuilder
    {
        if (is_array($val))
            return $qb->andWhere('SS.ss_id IN(:ids)')
                ->setParameter('ids', $val);

        return $qb->andWhere('SS.ss_id = :id')
            ->setParameter('id', $val);
    }

    /**
     * @param QueryBuilder $qb
     * @param string $val
     * @return QueryBuilder
     */
    protected function _filter_not_include_id(QueryBuilder $qb, string $val): QueryBuilder
    {
        return $qb->andWhere('SS.ss_id <> :id')
            ->setParameter('id', $val);
    }

    /**
     * @param QueryBuilder $qb
     * @param integer|array $val
     * @return QueryBuilder
     */
    protected function _filter_user_id(QueryBuilder $qb, mixed $val): QueryBuilder
    {
        if (is_array($val))
            return $qb->andWhere('SS.ss_user_id IN(:user_ids)')
                ->setParameter('user_ids', $val);

        return $qb->andWhere('SS.ss_user_id = :user_id')
            ->setParameter('user_id', $val);
    }

    /**
     * @param QueryBuilder $qb
     * @param string $val
     * @return QueryBuilder
     */
    protected function _filter_area_type(QueryBuilder $qb, string $val): QueryBuilder
    {
        return $qb->andWhere('SS.ss_area_type = :area_type')
            ->setParameter('area_type', $val);
    }

    /**
     * @param QueryBuilder $qb
     * @param array $val
     * @return QueryBuilder
     */
    protected function _filter_limit_offset(QueryBuilder $qb, array $val): QueryBuilder
    {
        return $qb->setMaxResults($val['limit'] ?? 30)
            ->setFirstResult($val['offset'] ?? 0);
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
            ->select('SS')
            ->from($this->getEntityName(), 'SS');

        // Filter
        if (isset($opts['params']) && is_array($opts['params'])) {
            foreach ($opts['params'] as $key => $val) {
                $this->{"_filter_{$key}"}($qb, $val);
            }
        }

        // Order
        if (isset($opts['order']) && is_array($opts['order'])) {
            foreach ($opts['order'] as $col => $mode)
                $qb->addOrderBy("SS.ss_{$col}", $mode);
        } else
            $qb->addOrderBy('SS.ss_time', 'DESC');

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

    /**
     * Add new Session
     * @param array $data
     * @return \Models\Entities\Session
     */
    public function insertData(array $data = [])
    {
        $entity = new EntitiesSession($data);
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        //	Return
        return $entity;
    }

    /**
     * Edit Session
     *
     * @param \Models\Entities\Session $entity
     * @param array $updateData
     * @return \Models\Entities\Session
     */
    public function updateData(EntitiesSession $entity, array $updateData): EntitiesSession
    {
        $entity->fromArray($updateData);
        $this->getEntityManager()->flush($entity);

        return $entity;
    }   

    /**
     * Get list session by condition
     *
     * @param array $opts
     * @return array
     */
    public function quickSearch(array $opts = []): array
    {
        $limit = $opts['limit'] ?? 30;
        $offset = $opts['offset'] ?? 0; 

        $selects = 'partial SS.{ss_id, ss_user_id}';
        if ($opts['cstCols'] && is_array($opts['cstCols'])) {
            $selects = 'partial SS.{' .implode(',', $opts['cstCols']). '}';
        }

        // Create query
        $mainQuery = $this->getEntityManager()
            ->createQueryBuilder()
            ->select($selects)
            ->from($this, 'SS')
            ->setMaxResults($limit)
            ->setFirstResult($offset);
        
        // Bind result
        $rs = $mainQuery->getQuery()->getArrayResult();

        $result = [];
        foreach ($rs as $row) {
            $result[$row['ss_id']] = $row;
        }
    }

    /**
     * Save session & device information
     *
     * @param array $data
     * @return integer
     */
    public function createDeviceInfo(array $data): int
    {
        return $this->getEntityManager()->getConnection()
            ->executeStatement(
                "SELECT fun_CreateDeviceInfo(:json_data)",
                ['json_data' => @json_encode($data)]
            ) ?? 0;
    }
}