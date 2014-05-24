<?php

namespace Info\ComplaintBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CompanyController extends Controller
{
    public function indexAction($id)
    {
    	$company = $this->getDoctrine()->getRepository('InfoComplaintBundle:Company')->find($id);

    	if(!$company)
    	{
    		throw $this->createNotFoundException('The company does not exist');
    	}

        return $this->render('InfoComplaintBundle:Company:companyPage.html.twig', array('name' => $company));

    }
}
