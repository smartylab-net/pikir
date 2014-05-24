<?php

namespace Info\ComplaintBundle\Controller;

use Info\ComplaintBundle\Entity\Complaint;
use Info\ComplaintBundle\Form\ComplaintType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CompanyController extends Controller
{
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

        return $this->render('InfoComplaintBundle:HomePage:company.html.twig', array('subcategory' => $subcategory));
    }

    public function indexAction($id)
    {
        $company = $this->getDoctrine()->getRepository('InfoComplaintBundle:Company')->find($id);

        if (!$company) {
            throw $this->createNotFoundException('The company does not exist');
        }
        return $this->render('InfoComplaintBundle:Company:companyPage.html.twig', array('name' => $company));
    }
}
