<?php

namespace Application\Sonata\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository {

    public function getModerators()
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
            ->from($this->_entityName, 'u')
            ->where('u.roles LIKE :moder')
            ->orWhere('u.roles LIKE :admin')
            ->setParameters(array('moder'=>'%ROLE_MODERATOR%', 'admin'=>'%ADMIN%'));

        return $qb->getQuery()->getResult();
    }
}