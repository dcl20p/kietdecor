<?php
namespace Models\Repositories;

use Doctrine\ORM\QueryBuilder;
use Models\Entities\ProjectCate as EntitiesProjectCate;
use Models\Repositories\Abstracted\Repository;

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
    }
}