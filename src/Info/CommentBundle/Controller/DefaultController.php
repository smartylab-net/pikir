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
            $this->getMailer()->sendEmailToComplaintAuthor($complaint, $comment);

            return $this->render('InfoCommentBundle:Default:comment.html.twig',
                array('node' => $comment, 'complaint' => $complaint, 'user' => $this->getUser())
            );
        }
        return new JsonResponse($form->getErrorsAsString(), 400);
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
            if ($this->shouldSendEmailToCommentAuthor($newComment, $comment)) {
                $this->getMailer()->sendEmailToCommentAuthor($complaint, $newComment, $comment);
            } elseif ($this->shouldSendEmailToComplaintAuthor($complaint, $newComment)){
                $this->getMailer()->sendEmailToComplaintAuthor($complaint, $newComment);
            }

            return $this->render('InfoCommentBundle:Default:comment.html.twig',
                array('node' => $newComment, 'complaint' => $complaint, 'user' => $this->getUser())
            );
        }
        return new JsonResponse('Неправильный формат данных', 400);
    }


    /**
     * @param $complaint
     * @param $newComment
     * @return bool
     */
    private function shouldSendEmailToComplaintAuthor(Complaint $complaint,Comment $newComment)
    {
        return $complaint != null && $complaint->getAuthor() != null && $complaint->getAuthor()->getEmailOnNewComment() && $complaint->getAuthor() != $newComment->getUser();
    }

    /**
     * @param $newComment
     * @param $answeredComment
     * @return bool
     */
    private function shouldSendEmailToCommentAuthor(Comment $newComment,Comment $answeredComment)
    {
        return $answeredComment != null && $answeredComment->getUser() != null && $answeredComment->getUser()->getEmailOnReplyToComment() && $answeredComment->getUser() != $newComment->getUser();
    }

    private function getMailer()
    {
        return $this->get('info_complaint.mailer');
    }
}