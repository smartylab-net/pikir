<?php

namespace Info\ComplaintBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('InfoComplaintBundle:Default:index.html.twig', array('name' => $name));
    }
}
