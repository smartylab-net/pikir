<?php


namespace Info\NotificationBundle\Twig;

use Info\ComplaintBundle\Form\SearchType;
use Symfony\Component\DependencyInjection\Container;

class NotificationExtension extends \Twig_Extension {

    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container) {

        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            'notification_header' => new \Twig_Function_Method($this, 'notificationHeader', array('is_safe' => array('html'))),
        );
    }

    public function notificationHeader()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $notificationRepository = $this->container->get('doctrine')->getRepository('InfoNotificationBundle:Notification');
        $notifications = $notificationRepository->findBy(array('user' => $user), array('id' => 'desc'), 4);
        $count = $this->container->get('doctrine.orm.entity_manager')
            ->createQuery("select count(n) from Info\NotificationBundle\Entity\Notification n WHERE n.user = :user and n.read = FALSE")
            ->setParameter('user', $user)
            ->getSingleScalarResult();
        $templating = $this->container->get('templating');
        return $templating->render('InfoNotificationBundle:Default:header.html.twig', array('notifications' => $notifications, 'count' => $count));
    }

    public function getName()
    {
        return 'notifications';
    }
}