<?php
/**
 * Created by PhpStorm.
 * User: Aykut
 * Date: 5/24/14
 * Time: 2:28 PM
 */

namespace Info\ComplaintBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CompanyRepository extends EntityRepository
{
    public function findLike($name)
    {
        $arr = array();
        $result= $this->createQueryBuilder('p')
            ->select('p.name')
            ->where('p.name like :name')
            ->setParameter('name',$name . '%')->getQuery()->getArrayResult();
        foreach ($result as $r) {
            $arr[] = $r['name'];
        }
        return $arr;

    }
}