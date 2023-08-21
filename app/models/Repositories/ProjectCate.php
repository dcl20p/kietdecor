<?php
namespace Models\Repositories;

use Doctrine\ORM\QueryBuilder;
use Laminas\Cache\Storage\StorageInterface;
use Models\Entities\ProjectCate as EntitiesProjectCate;
use Models\Repositories\Abstracted\Repository;
use Zf\Ext\CacheCore;
use Zf\Ext\LaminasRedisCache;

class ProjectCate extends Repository
{
    /**
     * @param QueryBuilder $qb
     * @param integer $val
     */
    protected function _filter_parent_id(QueryBuilder $qb, int $val): QueryBuilder
    {
        return $qb->andWhere('PRC.prc_parent_id = :parent_id')
            ->setParameter('parent_id', $val);
    }

    /**
     * @param QueryBuilder $qb
     * @param boolean $val
     */
    protected function _filter_only_parent(QueryBuilder $qb, bool $val): QueryBuilder
    {
        if ($val)
            return $qb->andWhere('PRC.prc_parent_id IS NULL');

        return $qb->andWhere('PRC.prc_parent_id IS NOT NULL');
    }

    /**
     * @param QueryBuilder $qb
     * @param integer|string $val
     * @return QueryBuilder
     */
    protected function _filter_status(QueryBuilder $qb, int|string $val): QueryBuilder
    {
        if (empty($val)) return $qb;
        return $qb->andWhere('PRC.prc_status = :status')
            ->setParameter('status', $val);
    }

    /**
     * Get list
     *
     * @param array $opts
     * @param boolean $fetchJoinKey
     * @return array|QueryBuilder
     */
    public function fetchOpts(array $opts = [], bool $fetchJoinKey = true): mixed
    {
        return $this->buildFetchOpts(
            $this->getEntityName(),
            'PRC',
            'prc',
            $opts,
            $fetchJoinKey
        );
    }

    /**
     * Add new prject cate
     * @param array $data
     * @return EntitiesProjectCate
     */
    public function insertData(array $data = []): EntitiesProjectCate
    {
        $entity = new EntitiesProjectCate($data);
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        
	    $this->clearDataFromCache();
        //	Return
        return $entity;
    }

    /**
     * Edit prject cate
     *
     * @param EntitiesProjectCate $entity
     * @param array $updateData
     * @return EntitiesProjectCate
     */
    public function updateData(EntitiesProjectCate $entity, array $updateData): EntitiesProjectCate
    {
        $entity->fromArray($updateData);
        $this->getEntityManager()->flush($entity);

	    $this->clearDataFromCache();
        return $entity;
    }

    /**
     * Change status
     *
     * @param array $opts
     * @return void
     */
    public function changeStatus(array $opts): void
    {
        $this->getEntityManager()
            ->getConnection()->executeStatement(
                'CALL sp_ChangeStatusProjectCate(:id,:status)',
                $opts
            );
            
	    $this->clearDataFromCache();
    }

    /**
     * Change status
     *
     * @param array $data
     * @return void
     */
    public function deleteData(array $data): void
    {
        $this->getEntityManager()
            ->getConnection()->executeStatement(
                'CALL sp_DeleteMultipleProjectCate(:ids)',
                ['ids' => @json_encode($data)]
            );
            
	    $this->clearDataFromCache();
    }

    const CACHE_KEY = 'project_cate';

    /**
     * Get cache core
     *
     * @param string $key
     * @return LaminasRedisCache
     */
    protected function getCacheCore(string $key = self::CACHE_KEY): LaminasRedisCache
    {
        return CacheCore::_getRedisCaches($key, [
            'lifetime'  => false,
            'namespace' => self::CACHE_KEY
        ]);
    }

    /**
     * Clear cache
     * @param string $key
     * @return bool
     */
    public function clearDataFromCache(string $key = self::CACHE_KEY): bool
    {
        if (!empty($key))
            return $this->getCacheCore()->clearByNamespace(self::CACHE_KEY);
        return $this->getCacheCore()->removeItem($key);
    }

    /**
     * Get data from cache
     *
     * @param array $opts
     * @param bool $useCache
     * @return array
     */
    public function getDataFromCache(array $opts = [], bool $useCache = true): array
    {
        $cache = $this->getCacheCore();
        $items = $cache->getItem(self::CACHE_KEY);

        if (null === $items || !$useCache) {
            $items = $this->getDataForSelect($opts);
            $cache->setItem(self::CACHE_KEY, $items);
        }
        unset($cache);

        return $items;
    }

    /**
     * @param array $opts
     * @return array
     */
    public function getDataForSelect(array $opts = []): array
    {
        $opts['params']['status'] = 1;
        $opts['resultMode'] = 'Array';
        $rs = $this->fetchOpts($opts);
        if (empty($rs)) return [];

        $result = [];
        foreach ($rs as $item) {
            $result[$item['prc_id']] = $item;
        }
        unset($rs);
        return $result;
    }
}