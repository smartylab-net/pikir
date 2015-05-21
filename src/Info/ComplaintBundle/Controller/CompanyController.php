<?php

namespace Info\ComplaintBundle\Controller;

use Info\ComplaintBundle\Entity\Company;
use Info\ComplaintBundle\Entity\Complaint;
use Info\ComplaintBundle\Form\CompanyType;
use Info\ComplaintBundle\Form\ComplaintType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;

class CompanyController extends Controller
{
    public function indexAction(Request $request, $id)
    {
    	$companyRepository = $this->getDoctrine()->getRepository('InfoComplaintBundle:Company');
    	$complaintRepository = $this->getDoctrine()->getRepository('InfoComplaintBundle:Complaint');
    	
    	$company = $companyRepository->find($id);
    	if(!$company || !$company->getEnabled())
    	{
    		throw $this->createNotFoundException('Компания не найдена');
    	}

        $imageurl = "";
        $media = $company->getLogo();
        if ($media) {
            $imageurl = $this->get('sonata.media.twig.extension')->path($media, 'reference');
        }

        $seoPage = $this->container->get('sonata.seo.page');

        $seoPage
            ->setTitle($company->getName())
            ->addMeta('name', 'description', $company->getAnnotation())
            ->addMeta('property', 'og:title', $company->getName())
            ->addMeta('property', 'og:type', 'website')
            ->addMeta('property', 'og:image', $imageurl)
            ->addMeta('property', 'og:url',  $request->getUri())
            ->addMeta('property', 'og:description', $company->getAnnotation())
        ;
        $this->get('strokit.breadcrumbs')->setParams(array('company_name' => $company->getName()));

		$complaintList = $complaintRepository->findByCompany($id);
        $rating = round($companyRepository->getComplaintsAverageRating($id));

        $form = $this->createForm(new ComplaintType());

        return $this->render('InfoComplaintBundle:Company:companyPage.html.twig', array('company' => $company,'complaintlist'=>$complaintList, 'average'=>$rating, 'form'=>$form->createView()));
    }

    public function createAction()
    {
        if (!$this->getUser())
        {
            throw new AccessDeniedException('Доступ к данной странице ограничен');
        }
        $company = new Company();
        $form = $this->createForm( new CompanyType(),$company);
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->submit($request);
            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($company);
                $em->flush();
                return $this->redirect($this->generateUrl('info_company_homepage',array('id'=>$company->getId())));
            }
            else
            {
                $this->container->get('session')->getFlashBag()->add('manager.company_edit_error', 'Профиль компании не сохранен, обнаружена ошибка');
                return $this->redirect($this->generateUrl('info_company_create'));
            }
        }

        return $this->render('InfoComplaintBundle:Company:create.html.twig',array('form'=>$form->createView()));
    }

    public function lastAddedCompaniesAction()
    {

        $companies = $this->getDoctrine()
            ->getRepository('InfoComplaintBundle:Company')
            ->findBy(array('enabled'=>true),array('id'=>'desc'),5);

        return $this->render('InfoComplaintBundle:Company:last_companies_list.html.twig', array('companies' => $companies));
    }
}
