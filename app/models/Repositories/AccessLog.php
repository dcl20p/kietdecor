<?php
namespace Models\Repositories;

use Models\Entities\AccessLog as EntitiesAccessLog;
use Zf\Ext\CacheCore;

class AccessLog extends Abstracted\Repository
{
    /**
     * Add new Access Log
     *
     * @param array $data
     * @return \Models\Entities\AccessLog
     */
    public function insertData(array $data = []): EntitiesAccessLog
    {
        $entity = new EntitiesAccessLog($data);

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush($entity);

        //	Return
        return $entity;
    }

    /**
     * Save access log to redis
     * @param array $data
     * @return mixed
     */
    public function putLogToRedis(array $data = []): mixed
    {
        $key = $this->makeLogKey($data['access'] ?? []);

        $cache = $this->getCacheCore();
        $oldData = $cache->getItem($key) ?? [];

        $data['files'] = $data['files'] ?? ($oldData['files'] ?? []);
        $rs = $cache->setItem($key, $data);

        unset($key, $cache, $oldData);
        return $rs;
    }

    /**
     * Generation access key
     * @param array $opts
     * @return integer
     */
    protected function makeLogKey(array $opts = []): int
    {
        return crc32("{$opts['al_site']}_{$opts['al_user_id']}");
    }

    const CACHE_KEY = 'access_logs';

    /**
     * Get Zend Cache
     * @param string $key
     * @return \Laminas\Cache\Storage\StorageInterface
     */
    protected function getCacheCore(string $key = self::CACHE_KEY)
    {
        return CacheCore::_getRedisCaches($key, [
            'lifetime' => false,
            'namespace' => self::CACHE_KEY
        ]);
    }

    /**
     * Clear cache
     * @param string $key
     * @return bool
     */
    public function clearDataFromCache(string $key = null): bool
    {
        if (!empty($key))
            return $this->getCacheCore()->clearByNamespace(self::CACHE_KEY);
        return $this->getCacheCore()->removeItem($key);
    }

    /**
     * Save access log to redis
     * @param array $opts
     */
    public function getLastAccessLog(array $opts = [])
    {
        
        $key = $this->makeLogKey($opts);
        
        $cache = $this->getCacheCore();
        $oldData = $cache->getItem($key) ?? [];
        
        unset($cache, $key);
        return $oldData;
    }

    /**
     * Clear access log
     * @param array $opts
     */
    public function clearAccessLog(array $opts = [])
    {
        return $this->getCacheCore()->removeItem(
            $this->makeLogKey($opts)
        );
    }
}