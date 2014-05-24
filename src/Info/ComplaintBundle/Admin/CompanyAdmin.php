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
           ->add('logo', 'sonata_type_model_list', array('required'=>false ,'label' => 'Логотип компании'), array('link_parameters' => array('context' =>'company')))
           ->add('annotation', 'textarea', array('label' => 'Описание компании'))
           ->add('address', 'text', array('label' => 'Адрес компании'))
           ->add('category', 'sonata_type_model_list',array('label' => 'Категория'))
       ;
   }

   // Fields to be shown on filter forms
   protected function configureDatagridFilters(DatagridMapper $datagridMapper)
   {
       $datagridMapper
           ->add('name')
           ->add('logo')
           ->add('annotation')
           ->add('address')
           ->add('complaints')
       ;
   }

   // Fields to be shown on lists
   protected function configureListFields(ListMapper $listMapper)
   {
       $listMapper
           ->addIdentifier('name')
           ->add('logo')
           ->add('annotation')
           ->add('address')

       ;
   }
}