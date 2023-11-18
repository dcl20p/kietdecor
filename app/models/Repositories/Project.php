<?php
namespace Models\Repositories;
use Doctrine\ORM\QueryBuilder;
use Models\Entities\Project as EntitiesProject;
use Models\Repositories\Abstracted\Repository;
use Zf\Ext\CacheCore;
use Zf\Ext\LaminasRedisCache;

class Project extends Repository
{
    const LIMIT = 30;
    const KEY_CACHE_PROJECT_ALL = 'project';
    const KEY_CACHE_PROJECT_BY_CATE_ID = self::KEY_CACHE_PROJECT_ALL . ':%s';

    /**
     * @param QueryBuilder $qb
     * @param integer|string $val
     * @return QueryBuilder
     */
    protected function _filter_status(QueryBuilder $qb, int|string $val): QueryBuilder
    {
        if (empty($val)) return $qb;
        return $qb->andWhere('PR.pr_status = :status')
            ->setParameter('status', $val);
    }

    /**
     * @param QueryBuilder $qb
     * @param integer|string $val
     * @return QueryBuilder
     */
    protected function _filter_prc_id(QueryBuilder $qb, int|string $val): QueryBuilder
    {
        if (empty($val)) return $qb;
        return $qb->andWhere('PR.pr_prc_id = :prc_id')
            ->setParameter('prc_id', $val);
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
            'PR', 'pr', $opts, $fetchJoinKey
        );
    }

    /**
     * Add new Service
     * @param array $data
     * @return EntitiesProject
     */
    public function insertData(array $data = []): EntitiesProject
    {
        $entity = new EntitiesProject($data);
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        $this->clearCacheProject(vsprintf(
            self::KEY_CACHE_PROJECT_BY_CATE_ID, [$data['pr_prc_id']]
        ));

        //	Return
        return $entity;
    }

    /**
     * Edit Service
     *
     * @param EntitiesProject $entity
     * @param array $updateData
     * @return EntitiesProject
     */
    public function updateData(EntitiesProject $entity, array $updateData): EntitiesProject
    {
        $entity->fromArray($updateData);
        $this->getEntityManager()->flush($entity);

        $this->clearCacheProject(vsprintf(
            self::KEY_CACHE_PROJECT_BY_CATE_ID, [$updateData['pr_prc_id']]
        ));

        return $entity;
    }

    /**
     * Get cache core
     *
     * @param string $key
     * @param integer|false $lifetime
     * @return LaminasRedisCache
     */
    protected function getCacheCore(string $key = self::KEY_CACHE_PROJECT_ALL, $lifetime = false): LaminasRedisCache
    {
        return CacheCore::_getRedisCaches($key, [
            'lifetime'  => false,
            'namespace' => self::KEY_CACHE_PROJECT_ALL
        ]);
    }

    /**
     * Clear cache
     * @param string $key
     * @return bool
     */
    public function clearDataFromCache(string $key = self::KEY_CACHE_PROJECT_ALL): bool
    {
        if (empty($key))
            return $this->getCacheCore()->clearByNamespace(self::KEY_CACHE_PROJECT_ALL);
        return $this->getCacheCore()->removeItem($key);
    }

    /**
     * Clear cache
     * @param string $key
     * @return void
     */
    public function clearCacheProject(string $key = self::KEY_CACHE_PROJECT_ALL): void
    {
        $this->clearDataFromCache();
        $this->clearDataFromCache($key);
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
        $items = $cache->getItem(self::KEY_CACHE_PROJECT_ALL);

        if (null === $items || !$useCache) {
            $items = $this->getDataForSelect($opts);
            $cache->setItem(self::KEY_CACHE_PROJECT_ALL, $items);
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
            $result[$item['pr_id']] = $item;
        }
        unset($rs);
        return $result;
    }

    /**
     * get data project from cache
     *
     * @param array $opts
     * @param bool $useCache
     * @return array
     */
    public function getDataFromCacheByCate(array $opts = [], bool $useCache = true): array
    {
        $params = isset($opts['params']) ? $opts['params'] : $opts;

        $limit  = $params['limit'] ?? self::LIMIT;
        $offset = $params['offset'] ?? 0;
        unset($opts['params']['limit'], $opts['params']['offset']);

        $keyCache = !empty($params['prc_id'])
            ? vsprintf(self::KEY_CACHE_PROJECT_BY_CATE_ID, [$params['prc_id']])
            : self::KEY_CACHE_PROJECT_ALL;
            
        $cache = $this->getCacheCore($keyCache, 86400);
        $items = $cache->getItem($keyCache, 0, -1);

        if (null === $items || !$useCache) {
            $items = $this->getDataForSelect($opts);
            $cache->setItem($keyCache, $items);
        }

        unset($cache);

        return array_slice($items, $offset, $limit);  
    }
}