<?php
/**
 * Created by PhpStorm.
 * User: bupychuk
 * Date: 01.06.14
 * Time: 19:33
 */

namespace Info\ComplaintBundle\Admin;


use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class ManagerRequestAdmin extends Admin {


    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('user',null,array('label'=>'Пользователь','editable'=>true))
            ->add('company',null,array('label'=>'Компания','editable'=>true))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'apply' => array('template'=>'InfoComplaintBundle:Admin:list_action_apply.html.twig'),
                    'delete' => array(),
                )
            ))

        ;
    }
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('create')
            ->remove('edit')
            ->add('apply', $this->getRouterIdParameter().'/apply');
    }
} 