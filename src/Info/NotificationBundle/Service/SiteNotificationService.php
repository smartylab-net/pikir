<?php


namespace Info\NotificationBundle\Service;


use Doctrine\ORM\EntityManager;
use Info\CommentBundle\Entity\Comment;
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

    public function notifyComplaintAuthor($newComment)
    {
        //TODO: save to DB and send pub
    }

    public function notifyCommentAuthor(Comment $newComment)
    {
        $user = $newComment->getParent()->getUser();
        $notification = new Notification();
        $notification->setMessage($this->templating->render("InfoNotificationBundle:Message:comment_author.html.twig", array('newComment' => $newComment)));
        $notification->setUser($user);
        $notification->setUrl($this->router->generate('info_complaint_complaint', array('id' => $newComment->getComplaint()->getId()))."#comment_".$newComment->getId());
        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        $this->WAMPClient->publish(self::$topic.$user->getId(), [$notification->getId()]);
    }
}