<?php

namespace Info\ComplaintBundle\Controller;

use Info\ComplaintBundle\Entity\Company;
use Info\ComplaintBundle\Entity\Complaint;
use Info\ComplaintBundle\Entity\ComplaintsCommentRating;
use Info\ComplaintBundle\Form\CompanyType;
use Info\ComplaintBundle\Form\ComplaintType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Doctrine\ORM\EntityRepository; 

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
                return $this->redirect($this->generateUrl('info_complaint_complaint',array('id'=>$complaint->getId())));
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
        if ($company!= null && $company->getManager()!= null && $company->getManager()->getEmailOnNewComplaint())
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

    public function deleteComplaintAction(Complaint $complaint)
    {
        if ($complaint == null) 
        {
            return $this->createNotFoundException();
        }


        if ($complaint->getAuthor() == null || $complaint->getAuthor()!=$this->getUser())
        {
            if(!$this->get('security.context')->isGranted('ROLE_MODERATOR'))
            {
                throw new AccessDeniedException('Доступ к данной странице ограничен');
            }
        }
        $company = $complaint->getCompany();
        

        $em = $this->getDoctrine()->getManager();
        $em->remove($complaint);
        $em->flush();

        $complaintRepository = $this->getDoctrine()->getManager()
            ->getRepository('InfoComplaintBundle:Complaint');
        $complaintList = $complaintRepository->findAll();

        return $this->redirect($this->generateUrl('info_complaint_homepage'));
    }

    public function editComplaintAction(Complaint $complaint)
    {
        if ($complaint == null)
        {
            return $this->createNotFoundException();
        }

        if ($complaint->getAuthor() == null || $complaint->getAuthor()!=$this->getUser() )
        {
            throw new AccessDeniedException('Доступ к данной странице ограничен');
        }

        $form = $this->createForm( new ComplaintType(),$complaint);
        $form->remove('company');

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->submit($request);
            $id = $complaint->getId();
            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($complaint);
                $em->flush();
                $this->container->get('session')->getFlashBag()->add('complaint_edit_success', 'Профиль отзывов обновлен');
                return $this->redirect($this->generateUrl('info_complaint_edit',array('complaint'=>$id)));
            }
            else
            {
                $this->container->get('session')->getFlashBag()->add('complaint_edit_error', 'Профиль отзывов не сохранен, обнаружена ошибка');
                return $this->redirect($this->generateUrl('info_complaint_edit',array('complaint'=>$id)));
            }
        }

        return $this->render('InfoComplaintBundle:Complaint:edit_complaint.html.twig',array('form'=>$form->createView(), 'complaint'=>$complaint));
    }

    public function voteAction($type, $id,  $voteType)
    {

        $error = false;
        $errorType = "";
        $em = $this->getDoctrine()->getManager();
        $complaintsCommentRating = new ComplaintsCommentRating();

        if($type === 'complaint')
        {
            $complaintRepository = $this->getDoctrine()->getRepository('InfoComplaintBundle:Complaint');
            $complaint = $complaintRepository->find($id);
            $voteValue = $complaint->getVote();

            //Check if this complaint has been voted
            $complaintsCommentRatingRep = $this->getDoctrine()->getRepository("InfoComplaintBundle:ComplaintsCommentRating");
            $objectVotedBefore = $complaintsCommentRatingRep->getIfComplaintVotedBefore($complaint);
            
        }else
        {
            $commentRepository = $this->getDoctrine()->getRepository('InfoCommentBundle:Comment');
            $comment = $commentRepository->find($id);
            $voteValue = $comment->getVote();
           
           //Check if this complaint has been voted
            $complaintsCommentRatingRep = $this->getDoctrine()->getRepository("InfoComplaintBundle:ComplaintsCommentRating");
            $objectVotedBefore = $complaintsCommentRatingRep->getIfCommentVotedBefore($comment);

        }
        
        
        //check is current user is registered or anonymous
        if($this->getUser())
        {
            if($type === 'complaint')
            {
                $userVoted = $complaintsCommentRatingRep->getIfComplaintVotedAndAuthorIsCurrentUser($complaint, $this->getUser());
            }else
            {
                $userVoted = $complaintsCommentRatingRep->getIfCommentVotedAndAuthorIsCurrentUser($comment, $this->getUser());
            }
            
            if($objectVotedBefore && $userVoted)
            {
    
                 $errorType = 'Вы уже голосовали';
                 $error = true;
                 
            }else{
             
                 if($type === 'complaint')
                {
                    //register the complaint in complaintsCommentRatingTable
                    
                    $complaintsCommentRating->setAuthor($this->getUser());
                    $complaintsCommentRating->setComplaint($complaint);
                    $em->persist($complaintsCommentRating);
                    
                    if ($voteType == 'plus') 
                    {

                        $complaint->setVote( $voteValue + 1);

                    }elseif($voteType == 'minus'){

                        $complaint->setVote( $voteValue - 1);

                    }
                    $em->persist($complaint);
                }else
                {

                    //register the complaint in complaintsCommentRatingTable
                    
                    $complaintsCommentRating->setAuthor($this->getUser());
                    $complaintsCommentRating->setComment($comment);
                    $em->persist($complaintsCommentRating);
                    
                    if ($voteType == 'plus') 
                    {

                        $comment->setVote( $voteValue + 1);

                    }elseif($voteType == 'minus'){

                        $comment->setVote( $voteValue - 1);

                    }

                    $em->persist($comment);

                }
                
                $em->flush(); 
            }

                
        }else{

            $request = $this->get('request');
            $cookie = $request->cookies->get('anonymous-vote');
            
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
            
            if($type === 'complaint')
            {
                $anonymVotedBefore = $complaintsCommentRatingRep->getIfComplaintVotedAndAuthorIsCurrentAnonymousUser($complaint, $cookie, $ip);
            }else
            {
                $anonymVotedBefore = $complaintsCommentRatingRep->getIfCommentVotedAndAuthorIsCurrentAnonymousUser($comment, $cookie, $ip);
            }
            
            if($objectVotedBefore && $anonymVotedBefore)
            {
                $errorType = 'Вы уже голосовали';
                $error = true;
            }
            else
            {

                if($type === 'complaint')
                {
                    
                    $complaintsCommentRating->setSessionCookie($cookie);
                    $complaintsCommentRating->setComplaint($complaint);
                    $complaintsCommentRating->setIp($ip);
                    $em->persist($complaintsCommentRating);
                    
                    if ($voteType == 'plus') 
                    {

                        $complaint->setVote( $voteValue + 1);

                    }elseif($voteType == 'minus'){

                        $complaint->setVote( $voteValue - 1);
                    }
                    $em->persist($complaint);
                }else
                {
                    
                    $complaintsCommentRating->setSessionCookie($cookie);
                    $complaintsCommentRating->setComment($comment);
                    $complaintsCommentRating->setIp($ip);
                    $em->persist($complaintsCommentRating);
                    
                    if ($voteType == 'plus') 
                    {

                        $comment->setVote( $voteValue + 1);

                    }elseif($voteType == 'minus'){

                        $comment->setVote( $voteValue - 1);
                    }
                    $em->persist($comment);
                }

                
                $em->flush(); 
            }
        }

        if($type === 'complaint')
        {
            $voteValue = $complaint->getVote();
        }else
        {
            $voteValue = $comment->getVote();
        }
        
        $jsonResponse = array('error'=>$error,'errorType'=>$errorType, 'voteValue'=>$voteValue); 
        return new JsonResponse($jsonResponse);    
    }

     public function preExecute()
    {
        $request = $this->get('request');
        $value = $request->cookies->get('anonymous-vote');
        
        if(!isset($value))
        {
            $cookie = new Cookie('anonymous-vote', time().sha1(time()), time() + 3600 * 24 * 7);
            $response = new Response();
            $response->headers->setCookie($cookie);
            return $response->send();
        }
    }
}
