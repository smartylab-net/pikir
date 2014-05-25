<?php

namespace Info\ComplaintBundle\Controller;

use Info\ComplaintBundle\Entity\Complaint;
use Info\ComplaintBundle\Form\ComplaintType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CompanyController extends Controller
{
    public function indexAction($id)
    {
    	$companyRepository = $this->getDoctrine()->getRepository('InfoComplaintBundle:Company');
    	$complaintRepository = $this->getDoctrine()->getRepository('InfoComplaintBundle:Complaint');
    	
    	$company = $companyRepository->find($id);
    	if(!$company)
    	{
    		throw $this->createNotFoundException('The company does not exist');
    	}
		
		$complaintList = $complaintRepository->findByCompany($id);
//    	if(!$complaintList)
//    	{
//    		throw $this->createNotFoundException('The complaintList does not exist');
//    	}

        $average = $companyRepository->getComplaintsAverageRating($id);
      
        return $this->render('InfoComplaintBundle:Company:companyPage.html.twig', array('company' => $company,'complaintlist'=>$complaintList, 'average'=>$average[0][1]));
    }
    public function showAllCompaniesAction($id)
    {
        $companies = $this->getDoctrine()
            ->getRepository('InfoComplaintBundle:Company')
            ->findBy(array('category' => $id));

        if (!$companies) {
            throw $this->createNotFoundException('The companies does not exist');
        }

        return $this->render('InfoComplaintBundle:Company:companies_list.html.twig', array('companies' => $companies));
    }

        public function showAllCategoriesAction()
    {
        $categories = $this->getDoctrine()
            ->getRepository("ApplicationSonataClassificationBundle:Category")
            ->findBy(array('enabled' => true, 'parent' => null));

        return $this->render('InfoComplaintBundle:HomePage:categories.html.twig', array('categories' => $categories));
    }

        public function showCategoryAction($id)
    {
        $subcategory = $this->getDoctrine()
            ->getRepository("ApplicationSonataClassificationBundle:Category")
            ->findBy(array('parent' => $id));

        if (!$subcategory) {
            throw $this->createNotFoundException('The companies does not exist');
        }

        return $this->render('InfoComplaintBundle:HomePage:company.html.twig', array('subcategory' => $subcategory));
    }
}
