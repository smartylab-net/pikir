<?php
/**
 * Created by PhpStorm.
 * User: bupychuk
 * Date: 24.05.14
 * Time: 18:04
 */

namespace Info\ComplaintBundle\Controller;


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
            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($company);
                $em->flush();
                $this->container->get('session')->getFlashBag()->add('manager.company_edit_success', 'Профиль компании обновлен');
                return $this->redirect($this->generateUrl('info_manager_company_edit',array('id'=>$id)));
            }
            else
            {
                $this->container->get('session')->getFlashBag()->add('manager.company_edit_error', 'Профиль компании не сохранен, обнаружена ошибка');
                return $this->redirect($this->generateUrl('info_manager_company_edit',array('id'=>$id)));
            }
        }

        return $this->render('InfoComplaintBundle:Manager:edit_company.html.twig',array('form'=>$form->createView()));
    }
} 