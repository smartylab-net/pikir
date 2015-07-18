<?php
/**
 * Created by PhpStorm.
 * User: bupychuk
 * Date: 24.05.14
 * Time: 18:04
 */

namespace Info\ComplaintBundle\Controller;


use FOS\UserBundle\Model\UserInterface;
use Info\ComplaintBundle\Entity\Company;
use Info\ComplaintBundle\Entity\ManagerRequest;
use Info\ComplaintBundle\Form\CompanyType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ManagerController extends Controller{

    public function editCompanyAction($slug)
    {
        $companyRepository = $this->getDoctrine()->getRepository('InfoComplaintBundle:Company');
        $company = $companyRepository->findOneBy(array('slug'=>$slug));
        if ($company == null)
        {
            return $this->createNotFoundException();
        }

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')
            && ($company->getManager() == null || $company->getManager()!=$this->getUser()))
        {
            throw new AccessDeniedException('Доступ к данной странице ограничен');
        }

        $breadcrumbExtension = $this->get('strokit.breadcrumbs');
        $breadcrumbExtension->setParams(array('company_name' => $company->getName()));
        $form = $this->createForm( new CompanyType(),$company);
        $request = $this->getRequest();
        $message = null;
        if ($request->getMethod() == 'POST') {
            $form->submit($request);
            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($company);
                $em->flush();
                return $this->redirect($this->generateUrl('info_company_homepage',array('slug'=>$slug)));
            }
            else
            {
                $message = 'Профиль компании не сохранен, обнаружена ошибка';
            }

        }
        return $this->render('@InfoComplaint/Company/create_edit.html.twig',array('form'=>$form->createView(), 'message' => $message));
    }

    public function myCompaniesListAction()
    {

        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $companies = $this->getDoctrine()->getRepository('InfoComplaintBundle:Company')->findByManager($this->getUser());
        return $this->render('InfoComplaintBundle:Manager:company_list.html.twig',array('companies'=>$companies));
    }

    public function askBeManagerAction(Company $company)
    {
        $user = $this->getUser();
        $response = new JsonResponse();
        if ($user!= null && $this->getRequest()->isXmlHttpRequest() && $this->getRequest()->isMethod('POST'))
        {
            if ($company->getManager()!=null)
            {
                $response->setData(array('message'=>'У данной компании уже есть представитель'));
                $response->setStatusCode(400);
            }
            elseif (is_object($this->getDoctrine()->getRepository('InfoComplaintBundle:ManagerRequest')->findOneBy(array('user'=>$user,'company'=>$company))))
            {

                $response->setData(array('message'=>'Вы уже отправляли запрос'));
                $response->setStatusCode(400);
            }
            else
            {
                $managerRequest = new ManagerRequest($user,$company);
                $em = $this->getDoctrine()->getManager();
                $em->persist($managerRequest);
                $em->flush();
                $response->setData(array('message'=>'Ваш запрос принят'));
            }

        }
        return $response;
    }
}