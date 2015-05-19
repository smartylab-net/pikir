<?php
 namespace Info\ComplaintBundle\Entity;

use Doctrine\ORM\EntityRepository; 

class CompanyRepository extends EntityRepository
{
	
	public function getComplaintsAverageRating($id)
	{

		return $this->getEntityManager()
            ->createQuery(
                "SELECT AVG(c.rating) FROM InfoComplaintBundle:Complaint c WHERE c.company = $id AND c.rating != 0"
            )->getSingleScalarResult();
	}

    public function findLike($name)
    {
        $result= $this->createQueryBuilder('p')
            ->select("p.id as value, p.name as label, 'info_company_homepage' as route")
            ->where('p.name like :name')
            ->andWhere('p.enabled = :enabled')
            ->setParameter('name','%'.$name . '%')
            ->setParameter('enabled',true)
            ->getQuery()->getArrayResult();
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

    public function getAll($rules = array()){
        $category = $this->createQueryBuilder('c');

        if (!empty($rules['enabled']))
        {
            $category->where('c.enabled = :a')
                ->setParameter('a', $rules['enabled']);
        }

        if (isset($rules['parent'])){
            $category->andWhere('c.parent = :a')
                ->setParameter('a', $rules['parent']);
        }
        else if (array_key_exists('parent',$rules))
        {

            $category->andWhere('c.parent is null');
        }

        return $category->getQuery()->getResult();
    }
}
