<?php

namespace Info\ComplaintBundle\Controller;

use Info\ComplaintBundle\Entity\Complaint;
use Info\ComplaintBundle\Form\ComplaintType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ComplaintController extends Controller
{

    public function complaintAction(Request $request)
    {
        $complaint = new Complaint();
        $form = $this->createForm(new ComplaintType(), $complaint);
        if ($request->isMethod('post')) {
            $form->submit($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $complaint->setCreated (new \DateTime());
                $em->persist($complaint);
                $em->flush();
                return $this->redirect($this->generateUrl('info_complaint_create'));
            }
        }
        $companyRepository = $this->getDoctrine()->getManager()
            ->getRepository('InfoComplaintBundle:Complaint');
        $companies=$companyRepository
            ->findAll();

        return $this->render('InfoComplaintBundle:Complaint:create_complaint.html.twig',
            array('form' => $form->createView(),
                'companies'=>$companies)
        );
    }

    public function allComplaintAction()
    {
        $posts = $this->getDoctrine()->getRepository('InfoComplaintBundle:Complaint')->findAll();

        return $this->render('InfoComplaintBundle:Complaint:allComplaint.html.twig', array(
            'posts' => $posts
        ));
    }

    public function getComplaintAction($id){

        $post = $this->getDoctrine()->getRepository('InfoComplaintBundle:Complaint')->find($id);

        return $this->render('InfoComplaintBundle:Complaint:complaint.html.twig',array('post'=>$post));
    }
}
