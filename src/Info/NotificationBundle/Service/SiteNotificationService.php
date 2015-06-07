<?php


namespace Info\NotificationBundle\Service;


use Doctrine\ORM\EntityManager;
use Info\CommentBundle\Entity\Comment;
use Info\ComplaintBundle\Entity\Complaint;
use Info\NotificationBundle\DBAL\NotificationTypeEnum;
use Info\NotificationBundle\Entity\Notification;
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
        $notification = new Notification();
        $notification->setType(NotificationTypeEnum::COMMENT_TO_COMPLAINT);
        $notification->setElementId($newComment->getId());
        $notification->setUser($user);
        $notification->setUrl($this->getCommentURL($newComment));
        $notification->setCreatedAt(new \DateTime());
        $notification->setUpdatedAt(new \DateTime());
        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        $this->WAMPClient->publish(self::$topic.$user->getId(), [$notification->getId()]);
    }

    public function notifyCommentAuthor(Comment $newComment)
    {
        $user = $newComment->getParent()->getUser();
        $notification = new Notification();
        $notification->setType(NotificationTypeEnum::COMMENT_REPLY);
        $notification->setElementId($newComment->getId());
        $notification->setUser($user);
        $notification->setUrl($this->getCommentURL($newComment));
        $notification->setCreatedAt(new \DateTime());
        $notification->setUpdatedAt(new \DateTime());
        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        $this->WAMPClient->publish(self::$topic.$user->getId(), [$notification->getId()]);
    }

    public function notifyManager(Complaint $complaint)
    {
        $user = $complaint->getCompany()->getManager();
        $notification = new Notification();
        $notification->setType(NotificationTypeEnum::COMPLAINT_TO_COMPANY);
        $notification->setElementId($complaint->getId());
        $notification->setUser($user);
        $notification->setUrl($this->router->generate('info_complaint_complaint', array('id' => $complaint->getId())));
        $notification->setCreatedAt(new \DateTime());
        $notification->setUpdatedAt(new \DateTime());
        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        $this->WAMPClient->publish(self::$topic.$user->getId(), [$notification->getId()]);
    }

    /**
     * @param Comment $newComment
     * @return string
     */
    private function getCommentURL(Comment $newComment)
    {
        return $this->router->generate('info_complaint_complaint', array('id' => $newComment->getComplaint()->getId())) . "#comment_" . $newComment->getId();
    }
}