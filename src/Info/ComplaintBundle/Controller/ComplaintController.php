<?php

namespace Info\ComplaintBundle\Controller;

use Info\ComplaintBundle\Entity\Company;
use Info\ComplaintBundle\Entity\Complaint;
use Info\ComplaintBundle\Form\CompanyType;
use Info\ComplaintBundle\Form\ComplaintType;
use Name;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ComplaintController extends Controller
{

    public function complaintAction($id)
    {
        $complaint = new Complaint();
        if ($id != null)
            $complaint->setCompany($this->getDoctrine()->getRepository('InfoComplaintBundle:Company')->find($id));
        $form = $this->createForm(new ComplaintType(), $complaint);
        $request = $this->getRequest();
        if ($request->isMethod('post')) {
            $form->submit($request);
            if($form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $complaint->setCreated (new \DateTime());
                if(is_null($complaint->getCompany())){

                    $company = new Company();
                    $em = $this->getDoctrine()->getManager();
                    $company->setName($request->get('company'));
                    $em->persist($company);
                    $complaint->setCompany($company);
                }
                $em->persist($complaint);
                $em->flush();
                return $this->redirect($this->generateUrl('info_complaint_list'));
            }
        }
        $companyRepository = $this->getDoctrine()->getManager()
            ->getRepository('InfoComplaintBundle:Complaint');
        $companies=$companyRepository
            ->findAll();

        return $this->render('InfoComplaintBundle:Complaint:create_complaint.html.twig',
            array('form' => $form->createView(),
                'companies' => $companies)
        );
    }

    public function allComplaintAction()
    {
        $postRepository = $this->getDoctrine()->getManager()
            ->getRepository('InfoComplaintBundle:Complaint');
        $complaintList = $postRepository
            ->findAll();
        return $this->render('InfoComplaintBundle:Complaint:allComplaint.html.twig', array('complaintlist' => $complaintList));
    }

    public function getComplaintAction($id)
    {

        $complaintlist = $this->getDoctrine()->getRepository('InfoComplaintBundle:Complaint')->find($id);

        return $this->render('InfoComplaintBundle:Complaint:complaint.html.twig', array(
            'complaint' => $complaintlist
        ));
    }
}
