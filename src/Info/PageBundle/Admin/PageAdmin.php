<?php

namespace Info\PageBundle\Admin;

use Info\PageBundle\DBAL\PageTypeEnum;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Knp\Menu\ItemInterface as MenuItemInterface;

class PageAdmin extends Admin
{
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('url')
            ->add('title')
            ->add('content')
        ;
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Страница')
            ->add('url')
            ->add('iconClass',null, array('label' => 'Иконка', 'attr' => array('placeholder' => 'md md-alarm')))
            ->add('title',null,array('label'=>'Заголовок'))
            ->add('content', 'ckeditor', array('attr' => array('class' => 'span10', 'rows' => 20), 'config_name' => 'cke_config'))
            ->end()
        ;
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('url')
            ->add('title')
        ;
    }


}