<?php
/**
 * Created by PhpStorm.
 * User: Aykut
 * Date: 5/24/14
 * Time: 1:47 PM
 */
namespace Info\ComplaintBundle\Controller;

use Info\ComplaintBundle\Form;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

use Info\ComplaintBundle\Entity\Company;
use Info\ComplaintBundle\Repository;
use Info\ComplaintBundle\Form\SearchType;
use Info\ComplaintBundle\Form\SearchHandler;

class SearchController extends Controller
{

    public function searchAction()
    {
        // Generation of the form
        $form = $this->container->get('form.factory')->createBuilder(new SearchType())->getForm();

        // return the form view
        return $this->render('InfoComplaintBundle:Default:search.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function autoCompleteAction()
    {
        $name = $this->getRequest()->query->get('term');
        $em = $this->get('doctrine.orm.entity_manager');
        $poem = $em->getRepository('InfoComplaintBundle:Company')->findLike($name);

        return new JsonResponse($poem);
    }

    public function completeAction()
    {
        return $this->render('@InfoComplaint/Default/complete.html.twig');
    }

    public function getResultsAction()
    {
        // We recover the user request
        $request = $this->container->get('request');

        // We need to create again a form object for the formHandler
        $form = $this->container->get('form.factory')->createBuilder(new searchType())->getForm();

        $formHandler = new SearchHandler($form, $request, $this->getDoctrine()->getManager());

        if ($formHandler->process()) {

            // title sent
            $name = $form['name']->getData();

            $repository = $this->getDoctrine()
                                ->getManager()
                                ->getRepository('InfoComplaintBundle:Company');

            $companies_list = $repository->findLike( $name );

            // return the results view
            return $this->render('InfoComplaintBundle:Default:results.html.twig', array(
                'companies' => $companies_list
            ));
        }

        // return the form view
        return $this->render('InfoComplaintBundle:Default:search.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}