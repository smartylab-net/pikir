<?php

namespace Info\ComplaintBundle\Controller;

use Behat\TestBundle\Form\ComplaintType;
use Info\ComplaintBundle\Entity\Complaint;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Request;

class ComplaintController extends Controller
{
    public function indexAction()
    {
        return $this->render('InfoComplaintBundle:Complaint:create_complaint.html.twig', array());
    }

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
                return $this->redirect($this->generateUrl('info_complaint_homepage'));
            }
        }
        return $this->render('InfoComplaintBundle:Complaint:create_complaint.html.twig', array('form' => $form->createView()));
    }

    public function allComplaintAction()
    {
        return $this->render('InfoComplaintBundle:Complaint:create_complaint.html.twig', array('allComplaint'=>$form->createView()));
    }
}
