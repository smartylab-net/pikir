<?php

namespace Info\CommentBundle\Controller;

use Info\CommentBundle\Form\CommentType;
use Info\ComplaintBundle\Entity\Complaint;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Info\CommentBundle\Entity\Comment;
use Symfony\Component\HttpFoundation\JsonResponse;

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

    public function createAction(Complaint $complaint)
    {
        $request = $this->getRequest();
        $comment = new Comment();
        $form = $this->createForm(new CommentType(), $comment);
        $em = $this->getDoctrine()->getManager();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $comment->setUser($this->getUser());
            $comment->setComplaint($complaint);
            $em->persist($comment);
            $em->flush();
            $this->sendEmailToComplaintAuthor($complaint, $comment);

            $node = array(
                'id' => $comment->getId(),
                'comment' => $comment->getComment(),
                'createdAt' => $comment->getCreatedAt()
            );
            return $this->render('InfoCommentBundle:Default:comment.html.twig',
                array('node' => $node, 'complaint' => $complaint, 'user' => $this->getUser())
            );
        }
        return new JsonResponse($form->getErrorsAsString(), 400);
    }

    public function replyAction(Complaint $complaint, Comment $comment)
    {
        $request = $this->getRequest();

        $commentContent = $request->request->get('comment');
        if ($request->isMethod("POST") && $commentContent) {
            $em = $this->getDoctrine()->getManager();

            $newComment = new Comment();
            $newComment->setUser($this->getUser());
            $newComment->setParent($comment);
            $newComment->setComment($commentContent);
            $em->persist($newComment);
            $em->flush();
            if (!($this->sendEmailToCommentAuthor($complaint, $newComment, $comment) && $complaint->getAuthor() == $comment->getUser()))
                $this->sendEmailToComplaintAuthor($complaint, $newComment);

            $node = array(
                'id' => $newComment->getId(),
                'comment' => $newComment->getComment(),
                'createdAt' => $newComment->getCreatedAt()
            );
            return $this->render('InfoCommentBundle:Default:comment.html.twig',
                array('node' => $node, 'complaint' => $complaint, 'user' => $this->getUser())
            );
        }
    }

    private function sendEmailToComplaintAuthor(Complaint $complaint, Comment $newComment)
    {
        if ($this->shouldSendEmailToComplaintAuthor($complaint, $newComment)) {
            $this->getMailer()->sendEmailMessage(
                array(
                    'comment' => $newComment,
                    'title' => 'Ваш отзыв прокомментировали',
                    'complaint' => $complaint
                ),
                $this->container->getParameter('email_from'),
                $complaint->getAuthor()->getEmail(),
                'InfoCommentBundle:Mail:complaint_comment.html.twig'
            );
            return true;
        }
        return false;
    }

    private function sendEmailToCommentAuthor(Complaint $complaint, Comment $newComment, Comment $answeredComment)
    {
        if ($this->shouldSendEmailToCommentAuthor($newComment, $answeredComment)) {
            $this->getMailer()->sendEmailMessage(
                array(
                    'newComment' => $newComment,
                    'answeredComment' => $answeredComment,
                    'title' => 'На ваш комментарий ответили',
                    'complaint' => $complaint
                ),
                $this->container->getParameter('email_from'),
                $complaint->getAuthor()->getEmail(),
                'InfoCommentBundle:Mail:comment_reply.html.twig'
            );
            return true;
        }
        return false;
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

    /**
     * @return \Strokit\CoreBundle\Mailer\Mailer
     */
    private function getMailer()
    {
        return $this->get('strokit_mailer');
    }
}