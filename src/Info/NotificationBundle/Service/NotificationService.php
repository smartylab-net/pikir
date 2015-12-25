<?php


namespace Info\NotificationBundle\Service;


use Application\Sonata\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Info\CommentBundle\Entity\Comment;
use Info\ComplaintBundle\Entity\Complaint;
use Info\ComplaintBundle\Entity\ComplaintsCommentRating;
use Info\ReportBundle\Entity\Report;

class NotificationService {
    /**
     * @var MailNotificationService
     */
    private $mailNotificationService;
    /**
     * @var SiteNotificationService
     */
    private $siteNotificationService;

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(MailNotificationService $mailNotificationService, SiteNotificationService $siteNotificationService, EntityManager $entityManager) {
        $this->mailNotificationService = $mailNotificationService;
        $this->siteNotificationService = $siteNotificationService;
        $this->em                      = $entityManager;
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

    public function notifyModerators(Report $report) {
        $moderators = $this->em->getRepository("ApplicationSonataUserBundle:User")->getModerators();
        if (is_array($moderators)) {
            /** @var User $moder */
            foreach ($moderators as $moder) {
                if ($moder->isEmailOnReport()) {
                    $this->mailNotificationService->sendEmailAboutReportToModerators($moder, $report);
                }
                $this->siteNotificationService->notifyModeratorsAboutReport($moder, $report);
            }
        }
    }

    /**
     * @param Complaint|Comment $element
     * @param ComplaintsCommentRating $vote
     */
    public function notifyElementLiked($element, ComplaintsCommentRating $vote) {
        if ($element instanceof Complaint) {
            $this->siteNotificationService->notifyComplaintLiked($element, $vote);
        }
    }
}