<?php
/**
 * Created by PhpStorm.
 * User: bupychuk
 * Date: 02.06.14
 * Time: 23:54
 */

namespace Info\ComplaintBundle\Controller;


use Info\ComplaintBundle\Entity\ManagerRequest;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminManagerController extends CRUDController{

    public function applyAction()
    {
        $id     = $this->get('request')->get($this->admin->getIdParameter());

        /** @var $managerRequest ManagerRequest */
        $managerRequest = $this->admin->getObject($id);


        if (!$managerRequest) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }
        //if ($managerRequest->getCompany() == null)

        try {
            $managerRequest->getCompany()->setManager($managerRequest->getUser());
            $this->admin->delete($managerRequest);

            if ($this->isXmlHttpRequest()) {
                return $this->renderJson(array('result' => 'ok'));
            }

            $this->addFlash(
                'sonata_flash_success',
                $this->admin->trans(
                    'flash_apply_success',
                    array('%name%' => $this->admin->toString($managerRequest)),
                    'SonataAdminBundle'
                )
            );

        } catch (ModelManagerException $e) {

            if ($this->isXmlHttpRequest()) {
                return $this->renderJson(array('result' => 'error'));
            }

            $this->addFlash(
                'sonata_flash_error',
                $this->admin->trans(
                    'flash_apply_error',
                    array('%name%' => $this->admin->toString($managerRequest)),
                    'SonataAdminBundle'
                )
            );
        }
        return new RedirectResponse($this->admin->generateUrl('list'));
    }
} 