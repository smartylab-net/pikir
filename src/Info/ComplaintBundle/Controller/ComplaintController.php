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
            if($form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($complaint);
                $em->flush();
                return $this->redirect($this->generateUrl('info_complaint_create'));
            }
        }
        return $this->render('InfoComplaintBundle:Complaint:create_complaint.html.twig', array('form' => $form->createView()));
    }

    public function allComplaintAction()
    {
        $postRepository = $this->getDoctrine()->getManager()
            ->getRepository('InfoComplaintBundle:Complaint');
        $complaintList = $postRepository
            ->findAll();
        return $this->render('InfoComplaintBundle:Complaint:allComplaint.html.twig', array('complaintlist' => $complaintList));
    }
}
