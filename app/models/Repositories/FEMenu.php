<?php
namespace Models\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Models\Entities\Constant as EntitiesConstant;
use Models\Entities\FEMenu as FEMenuEn;
use Zf\Ext\CacheCore;
use Models\Repositories\Abstracted\Repository;

class FEMenu extends Repository
{
    /**
     * @param QueryBuilder $qb
     * @param integer $val
     */
    protected function _filter_parent_id(QueryBuilder $qb, int $val): QueryBuilder
    {
        return $qb->andWhere('FE.menu_parent_id = :parent_id')
            ->setParameter('parent_id', $val);
    }

    /**
     * @param QueryBuilder $qb
     * @param string $val
     */
    protected function _filter_domain(QueryBuilder $qb, string $val): QueryBuilder
    {
        return $qb->andWhere('FE.menu_domain = :menu_domain')
            ->setParameter('menu_domain', $val);
    }

    /**
     * @param QueryBuilder $qb
     * @param integer $val
     */
    protected function _filter_status(QueryBuilder $qb, int $val): QueryBuilder
    {
        return $qb->andWhere('FE.menu_status = :status')
            ->setParameter('status', $val);
    }

     /**
     * @param Doctrine\ORM\QueryBuilder $qb
     * @param integer $val
     */
    protected function _filter_level(QueryBuilder $qb, int $val): QueryBuilder
    {
        return $qb->andWhere('FE.menu_level = :level')
        ->setParameter('level', $val);
    }

    /**
     * @param QueryBuilder $qb
     * @param integer $val
     */
    protected function _filter_limit(QueryBuilder $qb, int $val): QueryBuilder
    {
        return $qb->setMaxResults((int)$val)
		    ->setFirstResult(0);
    }

    /**
     * @param QueryBuilder $qb
     * @param bool $val
     */
    protected function _filter_parent_null(QueryBuilder $qb, bool $val)
    {
        if ( $val )
            return $qb->andWhere('FE.menu_parent_id IS NULL');
        return $qb->andWhere('FE.menu_parent_id IS NOT NULL');
    }

    /**
     * @param Doctrine\ORM\QueryBuilder $qb
     * @param string $val
     */
    protected function _filter_position(QueryBuilder $qb, string $val): QueryBuilder
    {
        return $qb->andWhere('FE.menu_position = :position')
            ->setParameter('position', $val);
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
            'FE', 'menu', $opts, $fetchJoinKey
        );
    }

    /**
     * Create tree
     *
     * @param array $items
     * @return array
     */
    protected function buildTree(array $items = []): array
    {
        $rs = [];
        foreach ($items as $item) {
            $item['childs'] = [];
            $vn = "item" . $item['menu_id'];
            ${$vn} = $item;
            if (!is_null($item['menu_parent_id'])) {
                $vp = "parent" . $item['menu_parent_id'];

                if (isset($rs[$item['menu_parent_id']])) {
                    ${$vp} = $rs[$item['menu_parent_id']];
                } else {
                    ${$vp} = [
                        'menu_id' => $item['menu_parent_id'],
                        'menu_parent_id' => null,
                        'childs' => []
                    ];
                    $rs[$item['menu_parent_id']] = &${$vp};
                }

                ${$vp}['childs'][$item['menu_id']] = &${$vn};
                $rs[$item['menu_parent_id']] = ${$vp};
            } else
                $rs[$item['menu_id']] = &${$vn};
        }
        return $rs;
    }

    const CACHE_KEY = 'fe_menus';
    const CACHE_TREE_KEY = 'fe_menu_trees';

    /**
     * Get tree data from cache
     *
     * @param array $opts
     * @param string $keyPrefix
     * @return array
     */
    public function getTreeDataFromCache(array $opts = [], string $keyPrefix = ''): array
    {
        $key = "{$keyPrefix}_" . self::CACHE_TREE_KEY;
        $cache = $this->getCacheCore();
        $items = $cache->getItem($key);
        if (null === $items) {

            $opts['resultMode'] = 'Array';
            $opts['order'] = ['parent_id' => 'ASC', 'order' => 'ASC'];

            $parentOpts = $opts;
            $length = FEMenuEn::MAX_DISPLAY_COLUMN;
            if ($parentOpts['params']['position'] == FEMenuEn::POSITION_FOOTER) {
                $parentOpts['params'] += [
                    'parent_null' => true,
                    'level' => '0',
                    'status' => 1,
                    'limit' => FEMenuEn::MAX_DISPLAY_FOOTER_COLUMN
                ];
                $length = null;
            } else {
                $parentOpts['params'] += [
                    'parent_null' => true,
                    'level' => '0',
                    'status' => 1,
                    'limit' => FEMenuEn::MAX_DISPLAY_ROOT_ITEM
                ];
            }

            $parents = $this->fetchOpts($parentOpts);
            $opts['params'] = array_replace(
                $opts['params'],
                ['parent_null' => false, 'status' => 1]
            );
            $childs = $this->buildTree(
                $this->fetchOpts($opts)
            );

            foreach ($childs as $id => $child) {
                foreach ($child['childs'] as $idC => $item) {
                    if (!empty($childs[$idC])) {
                        $childs[$id]['childs'][$idC]['childs'] = array_values($childs[$idC]['childs']);
                        unset($childs[$idC]);
                    }
                }
            }

            $rs = [];
            foreach ($parents as $parent) {
                if (!empty($childs[$parent['menu_id']]['childs'])) {
                    $parent['childs'] = array_slice(
                        array_values($childs[$parent['menu_id']]['childs']),
                        0,
                        $length
                    );
                } else
                    $parent['childs'] = [];
                $rs[] = $parent;
            }

            $cache->setItem($key, $rs);
            return $rs;
        } else
            return $items;
    }

    /**
     * Get data from cache
     *
     * @param array $opts
     * @return array
     */
    public function getDataFromCache(array $opts = []): array
    {
        $key = self::CACHE_KEY . '_data';
        $params = empty($opts['params']) ? [] : $opts['params'];
        unset($params['limit'], $params['offset']);

        if (!empty($params)) {
            ksort($params);
            $key .= '_' . crc32(json_encode($params));
        } else
            $key .= '_all';

        $cache = $this->getCacheCore($key);
        $items = $cache->getItem($key);
        if (empty($items) || true) {
            $items = $this->fetchOpts([
                'resultMode' => 'QueryBuilder',
                'params' => $params,
                'order' => ['time' => 'asc']
            ])
                ->indexBy('FE', 'FE.menu_id')
                ->getQuery()->getArrayResult();
            $cache->setItem($key, $items);
        }
        unset($cache);

        return $items;
    }

    /**
     * Get Zend cache
     *
     * @param string $nameSpace
     * @return Laminas\Cache\Storage\StorageInterface
     */
    protected function getCacheCore(string $nameSpace = self::CACHE_KEY)
    {
        return CacheCore::_getRedisCaches((self::CACHE_KEY), [
            'lifetime' => false,
            'namespace' => $nameSpace
        ]);
    }
}