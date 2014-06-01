<?php
/**
 * Created by PhpStorm.
 * User: bupychuk
 * Date: 31.05.14
 * Time: 21:02
 */

namespace Application\Sonata\UserBundle\Controller;


use Application\Sonata\UserBundle\Form\NotificationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class NotificationController extends Controller {

    //TODO: Настройка уведомлений по Email
    public function emailNotificationSettingsAction(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm(new NotificationType(),$user);

        if ($request->getMethod()=='POST')
        {
            $form->handleRequest($request);

            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
            }
        }

        return $this->render('ApplicationSonataUserBundle:Profile:edit_notification.html.twig',array('form'=>$form->createView()));
    }
} 