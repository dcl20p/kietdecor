<?php

namespace Models\Repositories;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Models\Entities\AccessLog;
use Models\Entities\Admin as EntitiesAdmin;
use Models\Utilities\FTS;
use Zf\Ext\CacheCore;
use Models\Repositories\Abstracted\Repository;
use Models\Entities\Admin as AdminEn;

class Admin extends Repository
{
    /**
     * @param \Doctrine\ORM\QueryBuilder $qb
     * @param integer|string $val
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function _filter_status(QueryBuilder $qb, int|string $val): QueryBuilder
    {
        if (empty($val)) return $qb;
        return $qb->andWhere('A.adm_status = :status')
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
        return $qb->andWhere($qb->expr()->exists(
            'SELECT AFTS.afts_adm_id ' .
            'FROM Models\Entities\AdminFts AFTS ' .
            'WHERE A.adm_id = AFTS.afts_adm_id ' .
            'AND MATCH(AFTS.afts_kw) AGAINST (:keyword BOOLEAN) > 0'
        ))
        ->setParameter('keyword', FTS::replaceFTSpecialChar($val));
    }
    /**
     * @param QueryBuilder $qb
     * @param string|array $val
     * @return QueryBuilder
     */
    protected function _filter_groupcode(QueryBuilder $qb, string|array $val): QueryBuilder
    {
        if (is_array($val))
            return $qb->andWhere('A.adm_groupcode IN(:groupcode)')
                ->setParameter('groupcode', $val);

        return $qb->andWhere('A.adm_groupcode = :groupcode')
            ->setParameter('groupcode', $val);
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
            'A', 'adm', $opts, $fetchJoinKey
        );
    }

    /**
     * Locked user
     * @param int $id
     * @param int $time
     * @return integer
     */
    public function lockedStatus(int $id, ?int $time = null): int
    {
        $time = $time ?? time();
        return $this->getEntityManager()
            ->getConnection()->executeStatement(
                "UPDATE tbl_admin SET adm_blocked_time = {$time} WHERE adm_id = {$id}"
            );
    }

    /**
     * Unlocked user after 1h
     * @param int $id
     * @return integer
     */
    public function openLockedStatus(int $id): int
    {
        return $this->getEntityManager()
            ->getConnection()->executeStatement(
                "UPDATE tbl_admin SET adm_blocked_time = 0 WHERE adm_id = {$id}"
            );
    }

    /**
     * Logout user
     *
     * @param integer|null $admId
     * @return boolean
     */
    public function logout(?int $admId = null): bool
    {
        if ($admId) {
            return (
                $this->getEntityManager()
                    ->getConnection()->executeQuery(
                        "UPDATE tbl_admin SET adm_last_access_time = :access_time, adm_ssid = '' WHERE adm_id = :adm_id",
                        [
                            'adm_id' => $admId,
                            'access_time' => time()
                        ]
                    )->rowCount() > 0
            );
        }
        return false;
    }

    /**
     * Add new Admin
     * @param array $data
     * @return \Models\Entities\Admin
     */
    public function insertData(array $data = [])
    {
        $entity = new EntitiesAdmin($data);
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush($entity);

        $this->getEntityManager()->getConnection()
            ->insert('tbl_admin_fts', [
                'afts_adm_id' => $entity->adm_id,
                'afts_adm_code' => $entity->adm_code,
                'afts_kw' => $this->makeFTSKeyword($entity),
            ]);
        //	Return
        return $entity;
    }

    /**
     * Edit admin
     *
     * @param \Models\Entities\Admin $entity
     * @param array $updateData
     * @return \Models\Entities\Admin
     */
    public function updateData(EntitiesAdmin $entity, array $updateData): EntitiesAdmin
    {
        $entity->fromArray($updateData);
        $this->getEntityManager()->flush($entity);

        $this->getEntityManager()->getConnection()
            ->update(
                'tbl_admin_fts',
                ['afts_kw' => $this->makeFTSKeyword($entity)],
                ['afts_adm_id' => $entity->adm_id]
            );

        return $entity;
    }

    /**
     * Delete user
     *
     * @param [type] $entity
     * @return boolean
     */
	public function deleteByEntity($entity): bool {
	    $this->getEntityManager()->remove($entity);
	    $this->getEntityManager()->flush();

	    //	Return
	    return true;
	}

    /**
     * Make Admin Full text search
     * @param \Models\Entities\Admin $entity
     */
    protected function makeFTSKeyword(EntitiesAdmin $entity): string
    {
        return FTS::replaceFTSpecialChar(implode(' ', [
            $entity->adm_code, $entity->adm_fullname,
            $entity->adm_email, $entity->adm_phone
        ]));
    }

    /**
     * Cache key for session
     * @var string
     */
    const SESSION_CACHE_KEY = 'PHP_SESS_manager';

    const CACHE_KEY = 'manager';

    /**
     * Get Zend cache
     *
     * @param string $key
     * @param boolean|integer $lifeTime
     * @return \Laminas\Cache\Storage\StorageInterface
     */
    protected function getCacheCore(string $key = self::CACHE_KEY, mixed $lifeTime = false): mixed
    {
        return CacheCore::_getRedisCaches($key, [
            'lifetime' => $lifeTime,
            'namespace' => $key
        ]);
    }

    /**
     * Delete session
     *
     * @param string $key
     * @return mixed
     */
    public function delSessionData(string $key): mixed
    {
        if (empty($key)) return false;

        $this->getCacheCore(
            self::SESSION_CACHE_KEY,
            ini_get('session.cookie_lifetime') ?? false
        )->removeItem($key);
        
        $sql = 'DELETE FROM tbl_session WHERE ss_id = :ss_id';
        return $this->getEntityManager()->getConnection()
            -> executeStatement($sql, ['ss_id' => $key]);
    }

    /**
     * Delete multi session
     *
     * @param array $key
     * @return mixed
     */
    public function delMultiSessionData(array $keys): mixed
    {
        if (empty($keys)) return false;

        $this->getCacheCore(
            self::SESSION_CACHE_KEY,
            ini_get('session.cookie_lifetime') ?? false
        )->removeItems($keys);
        
        $sql = 'DELETE FROM tbl_session WHERE ss_id IN (:ss_id)';
        return $this->getEntityManager()->getConnection()
            -> executeStatement(
                $sql, 
                ['ss_id' => $keys], 
                ['ss_id' => Connection::PARAM_STR_ARRAY]);
    }

    /**
     * Delete admin account
     *
     * @param AdminEn $user
     * @return integer
     */
    public function delData(AdminEn $user): int
    {
        $count = $this->getEntityManager()->getConnection()
            ->executeStatement("CALL sp_DelManagerUserById({$user->adm_id})");
        if ( $count > 0 ){
            $this->getEntityManager()
            ->getRepository(AccessLog::class)
            ->clearAccessLog([
                'al_site'   => APPLICATION_SITE,
                'al_user_id'=> $user->adm_id
            ]);
            
            @shell_exec('rm -rf ' . PUBLIC_PATH . "/uploads/manager/{$user->adm_code}");
        }
        return $count;
    }
}