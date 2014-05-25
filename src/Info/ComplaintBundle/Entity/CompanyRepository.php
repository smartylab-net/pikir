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

}
