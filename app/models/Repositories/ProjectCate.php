<?php
namespace Models\Repositories;
use Doctrine\ORM\QueryBuilder;
use Models\Entities\ProjectCate as EntitiesProjectCate;
use Models\Repositories\Abstracted\Repository;

class ProjectCate extends Repository
{
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
            'PRC', 'prc', $opts, $fetchJoinKey
        );
    }

    /**
     * Add new Service
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
     * Edit Service
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
}