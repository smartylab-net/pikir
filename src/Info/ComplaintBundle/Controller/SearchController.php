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
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

use Info\ComplaintBundle\Entity\Company;
use Info\ComplaintBundle\Form\SearchType;

class SearchController extends Controller
{
    const PAGE_LIMIT = 10;

    const COMPANY_PAGE_PARAM = 'company_page';
    const COMPLAINT_PAGE_PARAM = 'complaint_page';
    const COMMENT_PAGE_PARAM = 'comment_page';

    const ACTIVE_TAB = 'active';

    const COMPANY_TAB = 'company';
    const COMPLAINT_TAB = 'complaint';
    const COMMENT_TAB = 'comment';

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

            $paginator = $this->get('knp_paginator');

            $companies = $this->getCompanies($searchValue, $paginator);
            $complaints = $this->getComplaints($searchValue, $paginator);
            $comments = $this->getComments($searchValue, $paginator);

            return $this->render('InfoComplaintBundle:Search:results.html.twig', array(
                'companies' => $companies,
                'complaints' => $complaints,
                'comments' => $comments,
                'searchValue' => $searchValue,
                'active' => $request->get(self::ACTIVE_TAB, self::COMPANY_TAB),
                'form' => $form->createView()
            ));
        } else {
            throw new HttpException(400, $form->getErrorsAsString());
        }

    }

    /**
     * @param $searchValue
     * @param $paginator
     * @return mixed
     */
    private function getCompanies($searchValue, $paginator)
    {
        $companyRepository = $this->getDoctrine()->getRepository('InfoComplaintBundle:Company');
        $companyQuery = $companyRepository->findLike($searchValue);
        $companies = $paginator->paginate(
            $companyQuery, // target to paginate
            $this->get('request')->query->getInt(self::COMPANY_PAGE_PARAM, 1), // page parameter, now section
            self::PAGE_LIMIT, // limit per page
            array('pageParameterName' => self::COMPANY_PAGE_PARAM, 'sortDirectionParameterName' => 'company_sort')
        );
        $companies->setParam(self::ACTIVE_TAB, self::COMPANY_TAB);
        return $companies;
    }

    /**
     * @param $searchValue
     * @param $paginator
     * @return mixed
     */
    private function getComments($searchValue, $paginator)
    {
        $commentRepository = $this->getDoctrine()->getRepository('InfoCommentBundle:Comment');
        $commentQuery = $commentRepository->findLike($searchValue);
        $comments = $paginator->paginate(
            $commentQuery, // target to paginate
            $this->get('request')->query->getInt(self::COMMENT_PAGE_PARAM, 1), // page parameter, now section
            self::PAGE_LIMIT, // limit per page
            array('pageParameterName' => self::COMMENT_PAGE_PARAM, 'sortDirectionParameterName' => 'comment_sort')
        );
        $comments->setParam(self::ACTIVE_TAB, self::COMMENT_TAB);
        return $comments;
    }

    /**
     * @param $searchValue
     * @param $paginator
     * @return mixed
     */
    private function getComplaints($searchValue, $paginator)
    {
        $complaintRepository = $this->getDoctrine()->getRepository('InfoComplaintBundle:Complaint');
        $complaintQuery = $complaintRepository->findLike($searchValue);

        $complaints = $paginator->paginate(
            $complaintQuery, // target to paginate
            $this->get('request')->query->getInt(self::COMPLAINT_PAGE_PARAM, 1), // page parameter, now section
            self::PAGE_LIMIT, // limit per page
            array('pageParameterName' => self::COMPLAINT_PAGE_PARAM, 'sortDirectionParameterName' => 'complaint_sort')
        );
        $complaints->setParam(self::ACTIVE_TAB, self::COMPLAINT_TAB);
        return $complaints;
    }
}