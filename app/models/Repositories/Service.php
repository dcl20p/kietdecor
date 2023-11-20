<?php
namespace Models\Repositories;
use Doctrine\ORM\QueryBuilder;
use Models\Entities\Service as EntitiesService;
use Models\Repositories\Abstracted\Repository;
use Zf\Ext\CacheCore;
use Zf\Ext\LaminasRedisCache;

class Service extends Repository
{
    /**
     * @param QueryBuilder $qb
     * @param integer|string $val
     * @return QueryBuilder
     */
    protected function _filter_status(QueryBuilder $qb, int|string $val): QueryBuilder
    {
        if (empty($val)) return $qb;
        return $qb->andWhere('SV.sv_status = :status')
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
            'SV', 'sv', $opts, $fetchJoinKey
        );
    }

    /**
     * Add new Service
     * @param array $data
     * @return EntitiesService
     */
    public function insertData(array $data = []): EntitiesService
    {
        $entity = new EntitiesService($data);
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

	    $this->clearDataFromCache(self::CACHE_KEY);
        //	Return
        return $entity;
    }

    /**
     * Edit Service
     *
     * @param EntitiesService $entity
     * @param array $updateData
     * @return EntitiesService
     */
    public function updateData(EntitiesService $entity, array $updateData): EntitiesService
    {
        $entity->fromArray($updateData);
        $this->getEntityManager()->flush($entity);

	    $this->clearDataFromCache(self::CACHE_KEY);
        return $entity;
    }  

    const CACHE_KEY = 'service';

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
            $result[$item['sv_id']] = $item;
        }
        unset($rs);
        return $result;
    }
}