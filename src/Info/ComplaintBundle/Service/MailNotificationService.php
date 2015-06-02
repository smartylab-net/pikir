<?php


namespace Info\ComplaintBundle\Service;


use Info\CommentBundle\Entity\Comment;
use Info\ComplaintBundle\Entity\Complaint;

class MailNotificationService
{


    private $emailFrom;
    /** @var \Strokit\CoreBundle\Mailer\Mailer */
    private $mailer;

    public function __construct($mailer, $emailFrom)
    {
        $this->mailer = $mailer;
        $this->emailFrom = $emailFrom;
    }

    public function sendEmailToComplaintAuthor(Comment $newComment)
    {
        $this->mailer->sendEmailMessage(
            array(
                'comment' => $newComment,
                'title' => 'Ваш отзыв прокомментировали',
                'complaint' => $newComment->getComplaint()
            ),
            $this->emailFrom,
            $newComment->getComplaint()->getAuthor()->getEmail(),
            'InfoCommentBundle:Mail:complaint_comment.html.twig'
        );
    }

    public function sendEmailToCommentAuthor(Comment $newComment)
    {
        $answeredComment = $newComment->getParent();
        $this->mailer->sendEmailMessage(
            array(
                'newComment' => $newComment,
                'answeredComment' => $answeredComment,
                'title' => 'На ваш комментарий ответили',
                'complaint' => $newComment->getComplaint()
            ),
            $this->emailFrom,
            $newComment->getUser()->getEmail(),
            'InfoCommentBundle:Mail:comment_reply.html.twig'
        );
    }

    public function sendEmailToManager(Complaint $complaint)
    {
        $company = $complaint->getCompany();
        $this->mailer->sendEmailMessage(
            array('company' => $company, 'complaint' => $complaint),
            $this->emailFrom,
            $company->getManager()->getEmail(),
            'InfoComplaintBundle:Mail:complaint_create_manager.html.twig'
        );
    }
}