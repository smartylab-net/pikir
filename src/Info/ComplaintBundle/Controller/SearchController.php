<?php
/**
 * Created by PhpStorm.
 * User: Aykut
 * Date: 5/24/14
 * Time: 1:47 PM
 */
namespace Info\ComplaintBundle\Controller;

use Buzz\Message\Response;
use Info\ComplaintBundle\Form;
use Info\ComplaintBundle\Form\ComplaintType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

use Info\ComplaintBundle\Entity\Company;
use Info\ComplaintBundle\Repository;
use Info\ComplaintBundle\Form\SearchType;
use Info\ComplaintBundle\Form\SearchHandler;

class SearchController extends Controller
{
    public function autoCompleteAction()
    {
        $name = $this->getRequest()->query->get('term');
        $em = $this->get('doctrine.orm.entity_manager');
        $companies = $em->getRepository('InfoComplaintBundle:Company')->findLikeAutocomplete($name);

        return new JsonResponse($companies);
    }

    public function searchAction(Request $request)
    {

        $form = $this->createForm(new SearchType());
        $form->submit($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $searchValue = $data["search"];

            $companyRepository = $this->getDoctrine()
                ->getRepository('InfoComplaintBundle:Company');
            $complaintRepository = $this->getDoctrine()->getRepository('InfoComplaintBundle:Complaint');

            $companies = $companyRepository->findLike($searchValue);
            $complaints = $complaintRepository->findLike($searchValue);

            return $this->render('InfoComplaintBundle:Search:results.html.twig', array(
                'companies' => $companies,
                'complaints' => $complaints,
                'searchValue' => $searchValue
            ));
        } else {
            throw new HttpException(400, $form->getErrorsAsString());
        }

    }
}