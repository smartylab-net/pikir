<?php

namespace Info\CommentBundle\Controller;

use Info\CommentBundle\Form\CommentType;
use Info\ComplaintBundle\Entity\Complaint;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Info\CommentBundle\Entity\Comment;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function formAction($complaint)
    {
        $comment = new Comment();
        $form = $this->createForm(new CommentType(), $comment, array(
            'action' => $this->generateUrl('info_comment_create', array('complaint' => $complaint))
        ));
        return $this->render('InfoCommentBundle:Default:form.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function createAction(Request $request, Complaint $complaint)
    {
        $comment = new Comment();
        $form = $this->createForm(new CommentType(), $comment);
        $em = $this->getDoctrine()->getManager();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $comment->setUser($this->getUser());
            $comment->setComplaint($complaint);
            $em->persist($comment);
            $em->flush();
            $this->getNotificationService()->notifyComplaintAuthor($comment);

            return $this->render('InfoCommentBundle:Default:comment.html.twig',
                array('node' => $comment, 'complaint' => $complaint, 'user' => $this->getUser())
            );
        }
        return new JsonResponse(array('msg'=>$form->getErrorsAsString()), 400);
    }

    public function replyAction(Request $request, Complaint $complaint, Comment $comment)
    {
        $commentContent = $request->request->get('comment');
        if ($request->isMethod("POST") && $commentContent) {
            $em = $this->getDoctrine()->getManager();

            $newComment = new Comment();
            $newComment->setUser($this->getUser());
            $newComment->setParent($comment);
            $newComment->setComment($commentContent);
            $newComment->setComplaint($complaint);
            $em->persist($newComment);
            $em->flush();
            $this->getNotificationService()->notifyComplaintAuthor($newComment);
            $this->getNotificationService()->notifyCommentAuthor($newComment);

            return $this->render('InfoCommentBundle:Default:comment.html.twig',
                array('node' => $newComment, 'complaint' => $complaint, 'user' => $this->getUser())
            );
        }
        return new JsonResponse(array('msg'=>'Неправильный формат данных'), 400);
    }

    private function getNotificationService()
    {
        return $this->get('info_complaint.service.notification_service');
    }
}