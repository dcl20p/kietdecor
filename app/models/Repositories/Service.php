<?php
namespace Models\Repositories;
use Doctrine\ORM\EntityRepository;
use Models\Entities\Service as EntitiesService;

class Service extends EntityRepository
{
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