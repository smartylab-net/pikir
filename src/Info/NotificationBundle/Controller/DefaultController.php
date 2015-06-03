<?php

namespace Info\NotificationBundle\Controller;

use Info\NotificationBundle\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function showAction(Notification $notification)
    {
        if ($this->getUser() != null || $notification->getUser() != $this->getUser()) {
            return $this->createNotFoundException();
        }
        return $this->render('InfoNotificationBundle:Default:index.html.twig', array('notification' => $notification));
    }
}
