<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 24.05.14
 * Time: 15:54
 */

namespace Info\ComplaintBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CompanyRepository extends EntityRepository{

    public function getCompany($cId){
        if(!$cId){
            $query = $this->createQueryBuilder('q')
                ->where('q.enabled = true')
                ->getQuery();

            return $query;
        }
        else{
            $categoryQuery = $this->getEntityManager()->getRepository('ApplicationSonataClassificationBundle:Category')
                ->createQueryBuilder('c')
                ->select('c.id')
                ->where('c.parent = :c.parent')
                ->setParameter(':c.parent',$cId)
                ->getDQL();

            $query = $this->createQueryBuilder('q')
                ->where('q.category = :cid')
                ->orWhere($this->createQueryBuilder('q')->expr()->in(
                    'q.category',
                    $categoryQuery
                ))
                ->andWhere('q.enabled = true')
                ->setParameter(':cid',$cId)
                ->setParameter(':cparent',$cId)
                ->getQuery();

            return $query;

        }
    }
}
