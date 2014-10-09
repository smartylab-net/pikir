<?php
namespace Info\ComplaintBundle\Listener;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class VoteCookieResponseListener
{
	public function onKernelResponse(FilterResponseEvent $event)
	{
        $response  = $event->getResponse();
        $request = $event->getRequest();
        $value = $request->cookies->get('anonymous-vote');

        if(!isset($value))
        {
            $cookie = new Cookie('anonymous-vote', time().sha1(time()), time() + 3600 * 24 * 7);
            $response->headers->setCookie($cookie);
        }
	}
}