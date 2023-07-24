<?php
namespace Models\Repositories;

use Doctrine\ORM\EntityRepository;
use Models\Entities\Client as EntitiesClient;

class Client extends EntityRepository
{
    /**
     * Delete client
     *
     * @param array $options
     * @return mixed
     */
    public function unScriptClient(array $options = []): mixed
    {
        if (empty($options))
            return false;

        $area = $options['area_type'] ?? EntitiesClient::AREA_CUSTOMER;
        $options['area_type'] = $area;

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->delete($this->getEntityName(), 'C')
            ->setParameters($options)
            ->where('C.client_area_type = :area_type');

        $orX = $qb->expr()->orX();
        if (!empty($options['user_id']))
            $orX->add('C.client_user_id = :user_id');
        if (!empty($options['endpoint']))
            $orX->add('C.client_endpoint = :endpoint');
        if (!empty($options['auth_token']))
            $orX->add('C.client_auth_token = :auth_token');

        $qb->andWhere($orX);

        return $qb->getQuery()->execute();
    }
}