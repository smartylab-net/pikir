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
    		//throw this->createNotFoundException("No company found by this id".$id);
    	}

        return $this->render('InfoComplaintBundle:Default:index.html.twig', array('name' => $company));

    }
}
