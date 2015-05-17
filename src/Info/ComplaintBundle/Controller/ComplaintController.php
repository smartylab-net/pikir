<?php

namespace Info\ComplaintBundle\Controller;

use Info\ComplaintBundle\Entity\Company;
use Info\ComplaintBundle\Entity\Complaint;
use Info\ComplaintBundle\Entity\ComplaintsCommentRating;
use Info\CommentBundle\Entity\Comment;
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

    public function getComplaintAction($id)
    {

        $complaint = $this->getDoctrine()->getRepository('InfoComplaintBundle:Complaint')->find($id);
        if (!$complaint) {
            throw $this->createNotFoundException("Отзыв не найден");
        }
        $commentRep = $this->getDoctrine()->getRepository("InfoCommentBundle:Comment");
        $nodes = $commentRep->findBy(array('complaint'=>$complaint, 'lft'=>1));
        $options = array(
            'decorate' => true,
            'rootOpen' => function($tree) {
                if(count($tree) && ($tree[0]['lvl'] == 0)){
                    return '';
                } else {
                    return '<ul>';
                }
            },
            'rootClose' => function($tree) {
                if(count($tree) && ($tree[0]['lvl'] == 0)){
                    return '';
                } else {
                    return '</ul>';
                }
            },
            'childOpen' => '<li>',
            'childClose' => '</li>',
            'nodeDecorator' => function($node) use (&$complaint, &$commentRep)  {
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
        $page = $this->getRequest()->get('page', 0);
        $itemCount = 10;
        $complaints = $this->getDoctrine()
            ->getRepository('InfoComplaintBundle:Complaint')
            ->findBy(array(),array('id'=>'desc'), $itemCount, $page * $itemCount);

        $page++;
        if ($this->getRequest()->isXmlHttpRequest()) {
            return $this->render('InfoComplaintBundle:Complaint:complaints_list.html.twig', array('complaints' => $complaints, 'page' => $page));
        } else {
            return $this->render('InfoComplaintBundle:Complaint:last_complaints_list.html.twig', array('complaints' => $complaints, 'page' => $page));
        }
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

        if (
            ($complaint->getAuthor() == null || $complaint->getAuthor() != $this->getUser()) &&
            !$this->get('security.context')->isGranted('ROLE_MODERATOR')
        ) {
            throw new AccessDeniedException('Доступ к данной странице ограничен');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($complaint);
        $em->flush();

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

    public function voteComplaintAction(Complaint $complaint,  $voteType)
    {
        $error = false;
        $em = $this->getDoctrine()->getManager();
        $complaintsCommentRating = new ComplaintsCommentRating();

        $complaintsCommentRatingRep = $this->getDoctrine()->getRepository("InfoComplaintBundle:ComplaintsCommentRating");
        $objectVotedBefore = $complaintsCommentRatingRep->getIfComplaintVotedBefore($complaint);

        if($this->getUser())
        {
            $userVoted = $complaintsCommentRatingRep->getIfComplaintVotedAndAuthorIsCurrentUser($complaint, $this->getUser());

            if($objectVotedBefore && $userVoted)
            {

                $error = true;

            }else{

                $complaintsCommentRating->setAuthor($this->getUser());

            }

        }else
        {
            $request = $this->get('request');
            $cookie = $request->cookies->get('anonymous-vote');
            $ip = $request->getClientIp();
            $anonymVotedBefore = $complaintsCommentRatingRep->getIfComplaintVotedAndAuthorIsCurrentAnonymousUser($complaint, $cookie, $ip);

            if($objectVotedBefore && $anonymVotedBefore)
            {
                $error = true;
            }
            else{

                $complaintsCommentRating->setSessionCookie($cookie);
                $complaintsCommentRating->setIp($ip);
            }
        }

        if (!$error) {
            $complaintsCommentRating->setComplaint($complaint);
            $voteValue = $complaint->getVote();

            if ($voteType == 'plus')
            {
                $complaint->setVote( $voteValue + 1);

            }elseif($voteType == 'minus'){

                $complaint->setVote( $voteValue - 1);
            }

            $em->persist($complaintsCommentRating);
            $em->persist($complaint);
            $em->flush();
        }
        $jsonResponse = array('error'=>$error,'errorType'=>"Вы уже голосовали", 'voteValue'=>$complaint->getVote());
        return new JsonResponse($jsonResponse);

    }

    public function voteCommentAction(Comment $comment,  $voteType)
    {

        $error = false;
        $em = $this->getDoctrine()->getManager();
        $complaintsCommentRating = new ComplaintsCommentRating();
        $complaintsCommentRatingRep = $this->getDoctrine()->getRepository("InfoComplaintBundle:ComplaintsCommentRating");
        $objectVotedBefore = $complaintsCommentRatingRep->getIfCommentVotedBefore($comment);

        if($this->getUser())
        {

            $userVoted = $complaintsCommentRatingRep->getIfCommentVotedAndAuthorIsCurrentUser($comment, $this->getUser());

            if($objectVotedBefore && $userVoted)
            {
                $error = true;
            }else{
                $complaintsCommentRating->setAuthor($this->getUser());
                $complaintsCommentRating->setComment($comment);
           }
        }else{

            $request = $this->get('request');
            $cookie = $request->cookies->get('anonymous-vote');
            $ip = $request->getClientIp();
            $anonymVotedBefore = $complaintsCommentRatingRep->getIfCommentVotedAndAuthorIsCurrentAnonymousUser($comment, $cookie, $ip);

            if($objectVotedBefore && $anonymVotedBefore)
            {
                $error = true;
            }
            else
            {
                $complaintsCommentRating->setSessionCookie($cookie);
                $complaintsCommentRating->setIp($ip);
            }
        }

        if(!$error)
        {
            $voteValue = $comment->getVote();
            $complaintsCommentRating->setComment($comment);

            if ($voteType == 'plus')
            {
                $comment->setVote( $voteValue + 1);

            }elseif($voteType == 'minus'){

                $comment->setVote( $voteValue - 1);
            }

            $em->persist($complaintsCommentRating);
            $em->persist($comment);
            $em->flush();
        }


        $jsonResponse = array('error'=>$error,'errorType'=>"Вы уже голосовали", 'voteValue'=>$comment->getVote());
        return new JsonResponse($jsonResponse);
    }
}
