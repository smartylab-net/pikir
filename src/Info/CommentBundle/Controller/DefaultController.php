<?php

namespace Info\CommentBundle\Controller;

use Info\CommentBundle\Form\CommentType;
use Info\ComplaintBundle\Entity\Complaint;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Info\CommentBundle\Entity\Comment;

class DefaultController extends Controller
{
    public function formAction($complaint)
    {
        $comment = new Comment();
        $form = $this->createForm(new CommentType(), $comment, array(
            'action' => $this->generateUrl('info_comment_create', array('complaint'=>$complaint))
        ));
        return $this->render('InfoCommentBundle:Default:form.html.twig', array(
            'form'  => $form->createView()
        ));
    }

    public function createAction($complaint){
        $request = $this->getRequest();
        $comment = new Comment();
        $form = $this->createForm(new CommentType(), $comment, array(
            'action' => $this->generateUrl('info_comment_create', array('complaint'=>$complaint))
        ));
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("InfoComplaintBundle:Complaint");
        $entity = $repository->find($complaint);
        if (!$entity){
            $this->createNotFoundException("Отзыв не найден");
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            $comment->setUser($this->getUser());
            $comment->setComplaint($entity);
            $em->persist($comment);
            $em->flush();
            $this->sendEmailToComplaintAuthor($entity,$comment);
        }
        return $this->redirect($this->generateUrl('info_complaint_complaint', array('id'=>$entity->getId())));
    }

    public function replyAction($complaint, $comment){
        $request = $this->getRequest();
        /*$newComment = new Comment();
        $form = $this->createForm(new CommentType(), $newComment, array(
            'action' => $this->generateUrl('info_comment_reply', array('comment'=>$comment,'complaint'=>$complaint))
        ));
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("InfoComplaintBundle:Complaint");
        $entity = $repository->find($complaint);
        if (!$entity){
            $this->createNotFoundException("Отзыв не найден");
        }
        $repository = $em->getRepository("InfoCommentBundle:Comment");
        $entity = $repository->find($comment);
        if (!$entity){
            $this->createNotFoundException("Комментарий не найден");
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            $newComment->setUser($this->getUser());
            $newComment->setParent($entity);
            $em->persist($newComment);
            $em->flush();
            $this->sendEmailToComplaintAuthor($complaint,$newComment);
            return $this->redirect($this->generateUrl('info_complaint_complaint', array('id'=>$complaint)));
        }
        return $this->render('InfoCommentBundle:Default:form.html.twig', array(
            'form'  => $form->createView()
        ));*/

        $commentContent = $request->request->get('comment');
        if ($request->isMethod("POST") && $commentContent){
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository("InfoComplaintBundle:Complaint");
            $entityComplaint = $repository->find($complaint);
            if (!$entityComplaint){
                $this->createNotFoundException("Отзыв не найден");
            }
            $repository = $em->getRepository("InfoCommentBundle:Comment");
            $entity = $repository->find($comment);
            if (!$entity){
                $this->createNotFoundException("Комментарий не найден");
            }
            $newComment = new Comment();
            $newComment->setUser($this->getUser());
            $newComment->setParent($entity);
            $newComment->setComment($commentContent);
            $em->persist($newComment);
            $em->flush();
            if (!($this->sendEmailToCommentAuthor($entityComplaint,$newComment,$entity) && $entityComplaint->getAuthor() == $entity->getUser() ))
                $this->sendEmailToComplaintAuthor($entityComplaint,$newComment);
        }
        return $this->redirect($this->generateUrl('info_complaint_complaint', array('id'=>$complaint)));
    }

    private function sendEmailToComplaintAuthor($complaint, $newComment)
    {
        /** @var $complaint Complaint */
        /** @var $newComment Comment */
        if ($complaint!= null && $complaint->getAuthor()!= null && $complaint->getAuthor()->getEmailOnNewComment() && $complaint->getAuthor() != $newComment->getUser())
        {
            $mailer = $this->get('strokit_mailer');
            $mailer->sendEmailMessage(
                array('comment'=>$newComment, 'title' => 'Ваш отзыв прокомментировали','complaint'=>$complaint),
                $this->container->getParameter('email_from'),
                $complaint->getAuthor()->getEmail(),
                'InfoCommentBundle:Mail:complaint_comment.html.twig'
            );
            return true;
        }
        return false;
    }

    private function sendEmailToCommentAuthor($complaint, $newComment, $answeredComment)
    {
        /** @var $complaint Complaint */
        /** @var $newComment Comment */
        /** @var $answeredComment Comment */
        if ($answeredComment!= null && $answeredComment->getUser()!= null && $answeredComment->getUser()->getEmailOnReplyToComment() && $answeredComment->getUser() != $newComment->getUser())
        {
            $mailer = $this->get('strokit_mailer');
            $mailer->sendEmailMessage(
                array('newComment'=>$newComment,'answeredComment'=>$answeredComment, 'title' => 'На ваш комментарий ответили','complaint'=>$complaint),
                $this->container->getParameter('email_from'),
                $complaint->getAuthor()->getEmail(),
                'InfoCommentBundle:Mail:comment_reply.html.twig'
            );
            return true;
        }
        return false;
    }
}