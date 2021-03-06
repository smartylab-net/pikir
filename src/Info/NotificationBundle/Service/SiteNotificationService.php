<?php


namespace Info\NotificationBundle\Service;


use Application\Sonata\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Info\CommentBundle\Entity\Comment;
use Info\ComplaintBundle\Entity\Complaint;
use Info\ComplaintBundle\Entity\ComplaintsCommentRating;
use Info\NotificationBundle\DBAL\NotificationTypeEnum;
use Info\NotificationBundle\Entity\Notification;
use Info\ReportBundle\Entity\Report;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\TwigBundle\TwigEngine;

class SiteNotificationService {

    private static $topic = "pikir.notification";
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var WAMPClient
     */
    private $WAMPClient;
    /**
     * @var
     */
    private $templating;
    /**
     * @var Router
     */
    private $router;

    public function __construct(EntityManager $entityManager, WAMPClient $WAMPClient,TwigEngine $templating, Router $router) {
        $this->entityManager = $entityManager;
        $this->WAMPClient = $WAMPClient;
        $this->templating = $templating;
        $this->router = $router;
    }

    public function notifyComplaintAuthor(Comment $newComment)
    {
        $user = $newComment->getComplaint()->getAuthor();
        $this->createNotificationAndPublish($newComment, $user, NotificationTypeEnum::COMMENT_TO_COMPLAINT, $this->getCommentURL($newComment));
    }

    public function notifyCommentAuthor(Comment $newComment)
    {
        $user = $newComment->getParent()->getUser();
        $this->createNotificationAndPublish($newComment, $user, NotificationTypeEnum::COMMENT_REPLY, $this->getCommentURL($newComment));
    }

    public function notifyManager(Complaint $complaint)
    {
        $user = $complaint->getCompany()->getManager();
        $this->createNotificationAndPublish($complaint, $user, NotificationTypeEnum::COMPLAINT_TO_COMPANY, $this->getComplaintURL($complaint));
    }

    public function notifyModeratorsAboutReport(User $moder, Report $report)
    {
        if (!is_null($report->getComplaint())) {
            $url = $this->getComplaintURL($report->getComplaint());
            $type = NotificationTypeEnum::COMPLAINT_REPORT;
        } else {
            $url = $this->getCommentURL($report->getComment());
            $type = NotificationTypeEnum::COMMENT_REPORT;
        }
        $this->createNotificationAndPublish($report, $moder, $type, $url);
    }

    public function notifyComplaintLiked(Complaint $complaint, ComplaintsCommentRating $vote)
    {
        $this->createNotificationAndPublish($vote, $complaint->getAuthor(), NotificationTypeEnum::COMPLAINT_VOTE, $this->getComplaintURL($complaint));
    }

    /**
     * @param Comment $newComment
     * @return string
     */
    private function getCommentURL(Comment $newComment)
    {
        return $this->router->generate('info_complaint_complaint', array('id' => $newComment->getComplaint()->getId())) . "#comment_" . $newComment->getId();
    }

    /**
     * @param Complaint $complaint
     * @return string
     */
    private function getComplaintURL(Complaint $complaint)
    {
        return $this->router->generate('info_complaint_complaint', array('id' => $complaint->getId()));
    }

    /**
     * @param Comment|Complaint|Report|ComplaintsCommentRating $obj
     * @param User $user
     * @param String $type
     * @param String $url
     * @return Notification
     */
    private function createNotificationAndPublish($obj, User $user, $type, $url)
    {
        $notification = new Notification();
        $notification->setType($type);
        $notification->setElementId($obj->getId());
        $notification->setUser($user);
        $notification->setUrl($url);
        $notification->setCreatedAt(new \DateTime());
        $notification->setUpdatedAt(new \DateTime());
        $this->entityManager->persist($notification);
        $this->entityManager->flush();
        $this->WAMPClient->publish(self::$topic.$user->getId(), [$notification->getId()]);
        return $notification;
    }
}