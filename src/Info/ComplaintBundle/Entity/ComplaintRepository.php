<?php
 namespace Info\ComplaintBundle\Entity;

use Doctrine\ORM\EntityRepository; 

class ComplaintRepository extends EntityRepository
{


    public function findLike($search)
    {
        $result= $this->createQueryBuilder('p')
            ->select("p")
            ->where('p.text like :search')
            ->setParameter('search','%'.$search . '%')
            ->getQuery()->getResult();
        return $result;
    }

}
