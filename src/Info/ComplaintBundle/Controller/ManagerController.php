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
use Info\ComplaintBundle\Form\CompanyType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

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
    //TODO: Возможность выбора (Уведомления по Email)
    //TODO: Добавление компании
    //TODO: Стать представителем компании
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
}