<?php

namespace Info\ComplaintBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function homeAction()
    {
        return $this->render('InfoComplaintBundle:HomePage:index.html.twig');
    }

    public function uploadAction(Request $request) {

        $mediaManager = $this->get('sonata.media.manager.media');

        $provider = 'sonata.media.provider.image';
        $file = $request->files->get('file');

        if (!$request->isMethod('POST') || !$provider || null === $file) {
            throw $this->createNotFoundException();
        }

        $context = $request->get('context', $this->get('sonata.media.pool')->getDefaultContext());

        $media = $mediaManager->create();
        $media->setBinaryContent($file);

        $mediaManager->save($media, $context, $provider);

        $url = $this->get('sonata.media.twig.extension')->path($media, 'reference');
        return new JsonResponse($url);
    }
}
