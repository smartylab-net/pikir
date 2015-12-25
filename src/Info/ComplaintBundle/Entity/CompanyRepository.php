<?php
 namespace Info\ComplaintBundle\Entity;

use Application\Sonata\ClassificationBundle\Entity\Category;
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
            ->select("p")
            ->where('p.name like :name')
            ->andWhere('p.enabled = :enabled')
            ->setParameter('name','%'.$name . '%')
            ->setParameter('enabled',true)
            ->getQuery();
        return $result;
    }

    public function findLikeAutocomplete($name)
    {
        $result= $this->createQueryBuilder('p')
            ->select("p.id as value, p.name as label")
            ->where('p.name like :name')
            ->andWhere('p.enabled = :enabled')
            ->setParameter('name','%'.$name . '%')
            ->setParameter('enabled',true)
            ->getQuery()->getArrayResult();
        return $result;
    }

    public function getCompany($category){
        if(!$category){
            $query = $this->createQueryBuilder('q')
                ->where('q.enabled = true')
                ->getQuery();

            return $query;
        }
        else{
            $categories = $this->getChildCategories($category);

            $query = $this->createQueryBuilder('q')
                ->leftJoin('q.category','cat')
                ->where($this->createQueryBuilder('q')->expr()->in(
                    'q.category',
                    $categories
                ))
                ->andWhere('q.enabled = true')
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

    /**
     * @param Category $category
     * @return array
     */
    private function getChildCategories(Category $category)
    {
        $categories = [$category->getId()];
        foreach ($category->getChildren() as $child) {
            /** @var Category $child */
            $categories = array_merge($categories, $this->getChildCategories($child));
        }
        return $categories;
    }
}
