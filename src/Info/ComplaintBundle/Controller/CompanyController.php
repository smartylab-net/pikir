<?php

namespace Info\ComplaintBundle\Controller;

use Info\ComplaintBundle\Entity\Company;
use Info\ComplaintBundle\Entity\Complaint;
use Info\ComplaintBundle\Form\CompanyType;
use Info\ComplaintBundle\Form\ComplaintType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

class CompanyController extends Controller
{
    public function indexAction($id)
    {
    	$companyRepository = $this->getDoctrine()->getRepository('InfoComplaintBundle:Company');
    	$complaintRepository = $this->getDoctrine()->getRepository('InfoComplaintBundle:Complaint');
    	
    	$company = $companyRepository->find($id);
    	if(!$company || !$company->getEnabled())
    	{
    		throw $this->createNotFoundException('The company does not exist');
    	}

        $media = $company->getLogo();
        if ($media) {
            $mediaservice = $this->get('sonata.media.pool');
            $provider = $mediaservice
                ->getProvider($media->getProviderName());
            $format = $provider->getFormatName($media, 'big');
            $imageurl = $provider->generatePublicUrl($media, $format);
        }
        else {
            $imageurl = "";
        }

        $seoPage = $this->container->get('sonata.seo.page');

        $seoPage
            ->setTitle($company->getName())
            ->addMeta('name', 'description', $company->getAnnotation())
            ->addMeta('property', 'og:title', $company->getName())
            ->addMeta('property', 'og:type', 'website')
            ->addMeta('property', 'og:image', $imageurl)
            ->addMeta('property', 'og:url',  $this->getRequest()->getUri())
            ->addMeta('property', 'og:description', $company->getAnnotation())
        ;

		$complaintList = $complaintRepository->findByCompany($id);

        $average = $companyRepository->getComplaintsAverageRating($id);
      
        return $this->render('InfoComplaintBundle:Company:companyPage.html.twig', array('company' => $company,'complaintlist'=>$complaintList, 'average'=>$average[0][1]));
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

    public function lastAddedCompaniesAction()
    {

        $companies = $this->getDoctrine()
            ->getRepository('InfoComplaintBundle:Company')
            ->findBy(array('enabled'=>true),array('id'=>'desc'),5);

        return $this->render('InfoComplaintBundle:Company:last_companies_list.html.twig', array('companies' => $companies));
    }
}
