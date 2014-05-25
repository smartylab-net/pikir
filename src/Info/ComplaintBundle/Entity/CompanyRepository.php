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
            ->setParameter('name','%'.$name . '%')
            ->getQuery()->getArrayResult();

        $resultComplaint= $this->getEntityManager()->getRepository('InfoComplaintBundle:Complaint')->createQueryBuilder('p')
            ->select("p.id as value, p.title as label, 'info_complaint_complaint' as route")
            ->where('p.title like :name')
            ->setParameter('name','%'.$name . '%')
            ->getQuery()->getArrayResult();
        $result = array_merge($resultCompany,$resultComplaint);
        return $result;

    }

}
