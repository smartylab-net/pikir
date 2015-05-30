<?php
 namespace Info\ComplaintBundle\Entity;

use Doctrine\ORM\EntityRepository; 

class ComplaintRepository extends EntityRepository
{


    public function findLike($search)
    {
        $result= $this->createQueryBuilder('p')
            ->select("p")
            ->leftJoin('p.author','a')
            ->leftJoin('p.company','c')
            ->where('p.text like :search')
            ->orWhere('a.firstname like :search')
            ->orWhere('a.lastname like :search')
            ->orWhere('c.name like :search')
            ->setParameter('search','%'.$search . '%')
            ->getQuery();
        return $result;
    }

}
