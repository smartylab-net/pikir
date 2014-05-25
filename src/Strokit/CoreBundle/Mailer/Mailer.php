<?php

namespace Strokit\CoreBundle\Mailer;
/**
 * Класс для отправки писем
 *
 * Class Mailer
 * @package Strokit\CoreBundle\Mailer
 */
class Mailer
{
    protected $mailer;
    protected $twig;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig){
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    function sendEmailMessage($context, $fromEmail, $toEmail, $templateName = "StrokitCoreBundle::mail.html.twig")
    {
        $message = \Swift_Message::newInstance();
        $img['logo']   = $message->embed(\Swift_Image::fromPath('public/img/logo-latter.png'));
        $context['img'] = $img;
        $template = $this->twig->loadTemplate($templateName);
        $subject = $template->renderBlock('subject', $context);
        $htmlBody = $template->renderBlock('body_html', $context);
        if (is_null($message)){
            $message = \Swift_Message::newInstance();
        }
        $message->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail)
            ->setBody($htmlBody, 'text/html');

        $this->mailer->send($message);
    }
}
