<?php

namespace Info\ComplaintBundle\Controller;

use Info\ComplaintBundle\Entity\Complaint;
use Info\ComplaintBundle\Form\ComplaintType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CategoryController extends Controller
{
    public function showCategoryAction($categorySlug)
    {
        $category = null;

        $companyRepository = $this->getDoctrine()
            ->getRepository('InfoComplaintBundle:Company');

        $categoryRepository = $this->getDoctrine()
            ->getRepository('ApplicationSonataClassificationBundle:Category');

        if ($categorySlug)
        {
            $category = $categoryRepository->findOneBy(array('slug' => $categorySlug));

            if (!$category||!$category->getEnabled()) {
                throw $this->createNotFoundException('Page not found 404');
            }
            $this->get('strokit.breadcrumbs')->setParams(array('cId' => null));
            $breadcrumbManager = $this->get('bcm_breadcrumb.manager');
            $routeName = $this->getRequest()->get('_route');
            $breadcrumbElement = $category;
            do {
                $breadcrumbManager->addItem(
                    $routeName,
                    $breadcrumbElement->getName(), array('cId' => $breadcrumbElement->getId()));
            } while($breadcrumbElement = $breadcrumbElement->getParent());
        }

        $paginate = $companyRepository->getCompany($category);

        $paginator = $this->get('knp_paginator');

        $pagination = $paginator
            ->paginate($paginate,
            $this->get('request')->query->get('page', 1), 12);

        $pagination->setUsedRoute('info_complaint_category');

        return $this->render('InfoComplaintBundle:Company:getCategory.html.twig', array('pagination' => $pagination, 'catalog' => $category));

    }
}
