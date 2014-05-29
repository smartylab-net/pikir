<?php

namespace Info\ComplaintBundle\Controller;

use Info\ComplaintBundle\Entity\Company;
use Info\ComplaintBundle\Entity\Complaint;
use Info\ComplaintBundle\Form\CompanyType;
use Info\ComplaintBundle\Form\ComplaintType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ComplaintController extends Controller
{

    public function complaintAction($id)
    {
        $complaint = new Complaint();
        if ($id != null)
        {
            $company = $this->getDoctrine()->getRepository('InfoComplaintBundle:Company')->find($id);
            $complaint->setCompany($company);
        }
        $form = $this->createForm(new ComplaintType(), $complaint);
        $request = $this->getRequest();
        if ($request->isMethod('post')) {
            $form->submit($request);
            if($form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $complaint->setCreated (new \DateTime());
                $complaint->setAuthor($this->getUser());
                if(is_null($complaint->getCompany())){

                    $company = new Company();
                    $em = $this->getDoctrine()->getManager();
                    $company->setName($request->get('company'));
                    $em->persist($company);
                    $complaint->setCompany($company);
                }
                $em->persist($complaint);
                $em->flush();
                $this->sendEmailToManager($complaint);
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

        $complaint = $this->getDoctrine()->getRepository('InfoComplaintBundle:Complaint')->find($id);
        $commentRep = $this->getDoctrine()->getRepository("InfoCommentBundle:Comment");
        $nodes = $commentRep->findBy(array('complaint'=>$complaint, 'lft'=>1));
        $userRep = $this->getDoctrine()->getRepository('ApplicationSonataUserBundle:User');
        $options = array(
            'decorate' => true,
            'rootOpen' => '<ul>',
            'rootClose' => '</ul>',
            'childOpen' => '<li>',
            'childClose' => '</li>',
            'nodeDecorator' => function($node) use (&$complaint,&$commentRep)  {
                    return $this->renderView('InfoCommentBundle:Default:comment.html.twig',array('node'=>$node,'complaint'=>$complaint,'user'=>$commentRep->find($node['id'])->getUser()));
                }
        );
        $htmlTree = array();
        foreach($nodes as $node)
            $htmlTree[] = $commentRep->childrenHierarchy(
                $node, /* starting from root nodes */
                false, /* true: load all children, false: only direct */
                $options,
                true
            );

        return $this->render('InfoComplaintBundle:Complaint:complaint.html.twig', array(
            'complaint' => $complaint,
            'treeComments'   => $htmlTree
        ));
    }

    public function lastAddedComplaintsAction()
    {

        $complaints = $this->getDoctrine()
            ->getRepository('InfoComplaintBundle:Complaint')
            ->findBy(array(),array('id'=>'desc'),4);

        return $this->render('InfoComplaintBundle:Complaint:last_complaints_list.html.twig', array('complaints' => $complaints));
    }

    private function sendEmailToManager(Complaint $complaint)
    {
        $company = $complaint->getCompany();
        if ($company!= null && $company->getManager()!= null)
        {
            $mailer = $this->get('strokit_mailer');
            $mailer->sendEmailMessage(
                array('company'=>$company, 'complaint'=>$complaint),
                $this->container->getParameter('email_from'),
                $company->getManager()->getEmail(),
                'InfoComplaintBundle:Mail:complaint_create_manager.html.twig'
            );
        }
    }
}
