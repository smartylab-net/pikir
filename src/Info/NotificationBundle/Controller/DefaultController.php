<?php

namespace Info\NotificationBundle\Controller;

use Info\NotificationBundle\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DefaultController extends Controller
{
    public function showAction(Notification $notification)
    {
        $this->canRead($notification);
        return $this->render('InfoNotificationBundle:Message:index.html.twig', array('notification' => $notification));
    }

    public function openAction(Notification $notification) {
        $this->canRead($notification);
        $notification->setRead(true);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirect($notification->getUrl());
    }

    public function seeAllAction()
    {
        $notificationRepository = $this->getDoctrine()->getRepository('InfoNotificationBundle:Notification');
        $notifications = $notificationRepository->findUserNotifications($this->getUser());
        return $this->render('InfoNotificationBundle:Default:index.html.twig',array( 'notifications' => $notifications));
    }

    public function markAsReadAction(){
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }
        $this->getDoctrine()->getRepository('InfoNotificationBundle:Notification')->markAsRead($this->getUser());
        return new JsonResponse();
    }

    /**
     * @param Notification $notification
     */
    private function canRead(Notification $notification)
    {
        if ($this->getUser() == null || $notification->getUser() != $this->getUser()) {
            throw $this->createNotFoundException();
        }
    }
}
