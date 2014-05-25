<?php

namespace Info\ComplaintBundle\Controller;

use Info\ComplaintBundle\Entity\Complaint;
use Info\ComplaintBundle\Form\ComplaintType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CategoryController extends Controller
{
    public function showCategoryAction($cId)
    {
        $category = null;

        $companyRepository = $this->getDoctrine()
            ->getRepository('InfoComplaintBundle:Company');

        $categoryRepository = $this->getDoctrine()
            ->getRepository('ApplicationSonataClassificationBundle:Category');

        if(!$categoryRepository||!$companyRepository){
            throw new \Exception("Ошибка");
        }

        if ($cId)
        {
            $category = $categoryRepository->find($cId);

            if (!$category||!$category->getEnabled())
                throw $this->createNotFoundException('Page not found 404');
        }

        $cookieQuantity = $this->getRequest()->cookies->get("cookieQuantity", 12);

        $paginate = $companyRepository->getCompany($cId);

        $paginator = $this->get('knp_paginator');

        $pagination = $paginator
            ->paginate($paginate,
            $this->get('request')->query->get('page', 1), $cookieQuantity);

        $pagination->setUsedRoute('info_complaint_category');

        return $this->render('InfoComplaintBundle:Company:getCategory.html.twig', array('pagination' => $pagination, 'catalog' => $category, 'title' => $category!=null?$category->__toString():"Все категории"));

    }
}