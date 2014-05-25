<?php
 namespace Info\ComplaintBundle\Entity;

use Doctrine\ORM\EntityRepository; 

class CompanyRepository extends EntityRepository
{
	
	public function getComplaintsAverageRating($id)
	{

		return $this->getEntityManager()
            ->createQuery(
                "SELECT AVG(c.rating) FROM InfoComplaintBundle:Complaint c WHERE c.company = $id "
            )->getResult();
	}

    public function findLike($name)
    {
        $resultCompany= $this->createQueryBuilder('p')
            ->select("p.id as value, p.name as label, 'info_company_homepage' as route")
            ->where('p.name like :name')
            ->andWhere('p.enabled = :enabled')
            ->setParameter('name','%'.$name . '%')
            ->setParameter('enabled',true)
            ->getQuery()->getArrayResult();

        $resultComplaint= $this->getEntityManager()->getRepository('InfoComplaintBundle:Complaint')->createQueryBuilder('p')
            ->select("p.id as value, p.title as label, 'info_complaint_complaint' as route")
            ->where('p.title like :title')
            ->setParameter('title','%'.$name . '%')
            ->getQuery()->getArrayResult();
        $result = array_merge($resultCompany,$resultComplaint);
        return $result;

    }

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
                ->where('c.parent = :cparent')
                ->setParameter(':cparent',$cId)
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
