<?php
namespace Info\ComplaintBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class CompanyAdmin extends Admin
{
   // Fields to be shown on create/edit forms
   protected function configureFormFields(FormMapper $formMapper)
   {
       $formMapper
           ->add('name', 'text', array('label' => 'Название компании'))
           ->add('enabled',null,array('label'=>'Активность','required'=>false))
           ->add('approved',null,array('label'=>'Подтвержден','required'=>false))
           ->add('manager','sonata_type_model_list', array('required'=>false))
           ->add('logo', 'sonata_type_model_list', array('required'=>false ,'label' => 'Логотип компании'), array('link_parameters' => array('context' =>'company')))
           ->add('annotation', 'textarea', array('required'=>false,'label' => 'Описание компании'))
           ->add('address', 'text', array('required'=>false, 'label' => 'Адрес компании'))
           ->add('category', 'sonata_type_model_list',array('label' => 'Категория'))
       ;
   }

   // Fields to be shown on filter forms
   protected function configureDatagridFilters(DatagridMapper $datagridMapper)
   {
       $datagridMapper
           ->add('name')
           ->add('enabled',null,array('label'=>'Активность'))
           ->add('approved',null,array('label'=>'Подтвержден'))
           ->add('annotation')
           ->add('address')
       ;
   }

   // Fields to be shown on lists
   protected function configureListFields(ListMapper $listMapper)
   {
       $listMapper
           ->addIdentifier('name')
           ->add('enabled',null,array('label'=>'Активность','editable'=>true))
           ->add('approved',null,array('label'=>'Подтвержден','editable'=>true))
           ->add('manager',null,array('label'=>'Представитель','editable'=>true))
           ->add('logo')
           ->add('annotation')
           ->add('address')

       ;
   }
}