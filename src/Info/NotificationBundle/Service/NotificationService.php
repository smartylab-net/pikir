<?php


namespace Info\NotificationBundle\Service;


use Info\CommentBundle\Entity\Comment;
use Info\ComplaintBundle\Entity\Complaint;

class NotificationService {
    /**
     * @var MailNotificationService
     */
    private $mailNotificationService;
    /**
     * @var SiteNotificationService
     */
    private $siteNotificationService;

    public function __construct(MailNotificationService $mailNotificationService, SiteNotificationService $siteNotificationService) {
        $this->mailNotificationService = $mailNotificationService;
        $this->siteNotificationService = $siteNotificationService;
    }

    public function notifyCommentAuthor(Comment $newComment)
    {
        $answeredComment = $newComment->getParent();
        if ($answeredComment != null && $answeredComment->getUser() != null && $answeredComment->getUser() != $newComment->getUser()) {
            if ($answeredComment->getUser()->getEmailOnReplyToComment()) {
                $this->mailNotificationService->sendEmailToCommentAuthor($newComment);
            }
            if ($answeredComment->getUser()->isNotifyOnReplyToComment()) {
                $this->siteNotificationService->notifyCommentAuthor($newComment);
            }
        }
    }

    public function notifyComplaintAuthor(Comment $newComment)
    {
        $complaint = $newComment->getComplaint();

        if ($complaint != null && $complaint->getAuthor() != null && $complaint->getAuthor() != $newComment->getUser()) {
            if ($complaint->getAuthor()->getEmailOnNewComment()) {
                $this->mailNotificationService->sendEmailToComplaintAuthor($newComment);
            }
            if ($complaint->getAuthor()->isNotifyOnNewComplaint()) {
                $this->siteNotificationService->notifyComplaintAuthor($newComment);
            }
        }
    }

    public function notifyManager(Complaint $complaint)
    {
        $company = $complaint->getCompany();
        if ($company != null && $company->getManager() != null) {
            if ($company->getManager()->getEmailOnNewComplaint()) {
                $this->mailNotificationService->sendEmailToManager($complaint);
            }
            if ($company->getManager()->isNotifyOnNewComplaint()) {
                $this->siteNotificationService->notifyManager($complaint);
            }
        }
    }
}