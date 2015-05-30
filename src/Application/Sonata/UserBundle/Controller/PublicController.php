<?php


namespace Application\Sonata\UserBundle\Controller;


use Application\Sonata\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PublicController extends Controller {

    public function showAction(User $user) {

        $this->get('strokit.breadcrumbs')->setParams(array('user_name' => $user->__toString()));

        $complaintRepository = $this->getDoctrine()->getRepository('InfoComplaintBundle:Complaint');
        $complaintList = $complaintRepository->findBy(array('author'=>$user));
        return $this->render('@ApplicationSonataUser/Public/show.html.twig',
            array(
                'user' => $user,
                'complaintlist' => $complaintList
                ));
    }
}