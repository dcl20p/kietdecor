<?php
namespace Models\Repositories;
use Doctrine\ORM\EntityRepository;
use Models\Entities\Constant as EntitiesConstant;

class Constant extends EntityRepository
{
    /**
     * Get template contanst
     *
     * @param string $constantCode
     * @return array
     */
    public function fetchTmplMail(string $constantCode = ''): array
    {
        $constant = $this->getEntityManager()
            ->getRepository(EntitiesConstant::class)
            ->findOneBy(['constant_code' => $constantCode]);
        if ($constant) {
            return [
                'content' => $constant->constant_content,
                'sender' => $constant->constant_sender,
                'title' => $constant->constant_title,
                'receiver' => array_filter(
                    preg_split('/[\,\;]/', $constant->constant_receiver ?? '')
                )
            ];
        }
        return [];
    }
}