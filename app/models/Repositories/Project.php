<?php
namespace Models\Repositories;
use Doctrine\ORM\QueryBuilder;
use Models\Entities\Project as EntitiesProject;
use Models\Repositories\Abstracted\Repository;

class Project extends Repository
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

        return $entity;
    }  
}