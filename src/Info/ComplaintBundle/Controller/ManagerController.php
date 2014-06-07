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

    public function editCompanyAction(Company $company)
    {
        if ($company == null)
        {
            return $this->createNotFoundException();
        }

        if ($company->getManager() == null || $company->getManager()!=$this->getUser())
        {
            throw new AccessDeniedException('Доступ к данной странице ограничен');
        }
        $form = $this->createForm( new CompanyType(),$company);
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->submit($request);
            $id = $company->getId();
            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($company);
                $em->flush();
                $this->container->get('session')->getFlashBag()->add('manager.company_edit_success', 'Профиль компании обновлен');
                return $this->redirect($this->generateUrl('info_manager_company_edit',array('company'=>$id)));
            }
            else
            {
                $this->container->get('session')->getFlashBag()->add('manager.company_edit_error', 'Профиль компании не сохранен, обнаружена ошибка');
                return $this->redirect($this->generateUrl('info_manager_company_edit',array('company'=>$id)));
            }
        }

        return $this->render('InfoComplaintBundle:Manager:edit_company.html.twig',array('form'=>$form->createView()));
    }
    //TODO: функционал уведомлений (Как в FB) Новый отзыв, Новый коммент, Новый ответ на коммент, Новая просьба стать представителем компании
    //TODO: Добавление компании
    //TODO: ЛС
    public function myCompaniesListAction()
    {

        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $companies = $this->getDoctrine()->getRepository('InfoComplaintBundle:Company')->findByManager($this->getUser());
        return $this->render('InfoComplaintBundle:Manager:company_list.html.twig',array('companies'=>$companies));
    }

    //TODO: Стать представителем компании
    public function askBeManagerAction(Company $company)
    {
        $user = $this->getUser();
        $response = new JsonResponse();
//        if ($user!= null && $this->getRequest()->isXmlHttpRequest() && $this->getRequest()->isMethod('POST'))
        if (true)
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
                //TODO: прописать логику
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