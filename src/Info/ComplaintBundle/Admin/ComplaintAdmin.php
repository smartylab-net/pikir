<?php
namespace Info\ComplaintBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ComplaintAdmin extends Admin
{
   // Fields to be shown on create/edit forms
   protected function configureFormFields(FormMapper $formMapper)
   {
       $formMapper
           ->add('company', 'entity', array('class' => 'Info\ComplaintBundle\Entity\Company'))
           ->add('text', 'textarea', array('label' => 'Содержание отзыва'))
           ->add('rating', 'text', array('label' => 'Рейтинг'))           
       ;
   }

   // Fields to be shown on filter forms
   protected function configureDatagridFilters(DatagridMapper $datagridMapper)
   {
       $datagridMapper
           ->add('company')
           ->add('text')
           ->add('rating')
       ;
   }

   // Fields to be shown on lists
   protected function configureListFields(ListMapper $listMapper)
   {
       $listMapper
          ->add('company')
           ->add('text')
           ->add('rating')

       ;
   }
}