<?php

namespace Info\CommentBundle\Controller;

use Info\CommentBundle\Form\CommentType;
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
            $this->createNotFoundException("Жалоба не найдена");
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            $comment->setUser($this->getUser());
            $comment->setComplaint($entity);
            $em->persist($comment);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('info_complaint_complaint', array('id'=>$entity->getId())));
    }

    public function replyAction($complaint, $comment){
        $request = $this->getRequest();
        $newComment = new Comment();
        $form = $this->createForm(new CommentType(), $newComment, array(
            'action' => $this->generateUrl('info_comment_reply', array('comment'=>$comment,'complaint'=>$complaint))
        ));
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("InfoComplaintBundle:Complaint");
        $entity = $repository->find($complaint);
        if (!$entity){
            $this->createNotFoundException("Жалоба не найдена");
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
            return $this->redirect($this->generateUrl('info_complaint_complaint', array('id'=>$complaint)));
        }
        return $this->render('InfoCommentBundle:Default:form.html.twig', array(
            'form'  => $form->createView()
        ));
    }
}
