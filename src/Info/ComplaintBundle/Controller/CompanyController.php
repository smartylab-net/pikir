<?php

namespace Info\ComplaintBundle\Controller;

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
    	if(!$complaintList)
    	{
    		throw $this->createNotFoundException('The complaintList does not exist');
    	}

        $average = $companyRepository->getComplaintsAverageRating($id);
      
        return $this->render('InfoComplaintBundle:Company:companyPage.html.twig', array('company' => $company,'complaintlist'=>$complaintList, 'average'=>$average[0][1]));

    }
}
