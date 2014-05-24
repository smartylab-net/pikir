<?php

namespace Info\ComplaintBundle\Controller;

use Info\ComplaintBundle\Entity\Complaint;
use Info\ComplaintBundle\Form\ComplaintType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CompanyController extends Controller
{
    public function showAllCategoriesAction()
    {
        $categories = $this->getDoctrine()
            ->getRepository("ApplicationSonataClassificationBundle:Category")
            ->findBy(array('enabled'=>true,'parent'=>null));

        return $this->render('InfoComplaintBundle:HomePage:categories.html.twig',array('categories' => $categories));
    }

    public function showCategoryAction($id){
        $subcategory = $this->getDoctrine()
            ->getRepository("ApplicationSonataClassificationBundle:Category")
            ->findBy(array('parent'=>$id));

        return $this->render('InfoComplaintBundle:HomePage:company.html.twig',array('subcategory' => $subcategory));
    }
}