<?php
namespace Models\Repositories;
use Doctrine\ORM\QueryBuilder;
use Models\Entities\Service as EntitiesService;
use Models\Repositories\Abstracted\Repository;

class Service extends Repository
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

        return $entity;
    }  
}