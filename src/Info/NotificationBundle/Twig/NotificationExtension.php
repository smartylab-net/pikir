<?php


namespace Info\NotificationBundle\Twig;

use Info\ComplaintBundle\Form\SearchType;
use Info\NotificationBundle\DBAL\NotificationTypeEnum;
use Info\NotificationBundle\Entity\Notification;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Process\Exception\InvalidArgumentException;

class NotificationExtension extends \Twig_Extension {
    const NOTIFICATION_COUNT = 5;
    private $entityManager;
    private $notificationRepository;

    /**
     * @var Container
     */
    private $container;
    private $user;
    private $templating;

    public function __construct(Container $container) {

        $this->container = $container;
        $this->notificationRepository = $this->container->get('doctrine')->getRepository('InfoNotificationBundle:Notification');
        $this->entityManager = $this->container->get('doctrine.orm.entity_manager');
    }

    public function getFunctions()
    {
        return array(
            'notification_header' => new \Twig_Function_Method($this, 'notificationHeader', array('is_safe' => array('html'))),
            'notification_message' => new \Twig_Function_Method($this, 'notificationMessage', array('is_safe' => array('html'))),
        );
    }

    public function notificationMessage(Notification $notification)
    {
        $this->templating = $this->container->get('templating');
        switch ($notification->getType()) {
            case NotificationTypeEnum::COMMENT_REPLY :
                $comment = $this->entityManager->find("InfoCommentBundle:Comment", $notification->getElementId());
                $message = $this->templating->render("InfoNotificationBundle:Message:comment_reply.html.twig", array('comment' => $comment));
                break;
            case NotificationTypeEnum::COMMENT_TO_COMPLAINT :
                $comment = $this->entityManager->find("InfoCommentBundle:Comment", $notification->getElementId());
                $message = $this->templating->render("InfoNotificationBundle:Message:comment_complaint.html.twig", array('comment' => $comment));
                break;
            case NotificationTypeEnum::COMPLAINT_TO_COMPANY :
                $complaint = $this->entityManager->find("InfoComplaintBundle:Complaint", $notification->getElementId());
                $message = $this->templating->render("InfoNotificationBundle:Message:complaint_company.html.twig", array('complaint' => $complaint));
                break;
            case NotificationTypeEnum::COMPLAINT_REPORT :
                $report = $this->entityManager->find("InfoReportBundle:Report", $notification->getElementId());
                $message = $this->templating->render("InfoNotificationBundle:Message:complaint_report.html.twig", array('report' => $report));
                break;
            case NotificationTypeEnum::COMMENT_REPORT :
                $report = $this->entityManager->find("InfoReportBundle:Report", $notification->getElementId());
                $message = $this->templating->render("InfoNotificationBundle:Message:comment_report.html.twig", array('report' => $report));
                break;
            default :
                $logger = $this->container->get('logger');
                $logger->error('Notification type error, id = ' . $notification->getId());
                throw new InvalidArgumentException();
        }
        return $message;
    }

    public function notificationHeader()
    {
        $this->user = $this->container->get('security.context')->getToken()->getUser();
        $this->templating = $this->container->get('templating');
        $notifications = $this->notificationRepository
            ->findUserNotifications($this->user, self::NOTIFICATION_COUNT);
        $notificationsCount = $this->notificationRepository->countUnreadMessages($this->user);
        return $this->templating->render('InfoNotificationBundle:Default:header.html.twig',
            array(
                'notifications' => $notifications,
                'count' => $notificationsCount
            ));
    }

    public function getName()
    {
        return 'notifications';
    }
}