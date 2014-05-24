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
           ->add('name', 'text', array('label' => 'Company Name'))
           ->add('logo', 'text', array('label' => 'Company Logo'))
           ->add('annotation', 'textarea', array('label' => 'Company Annotation'))
           ->add('address', 'text', array('label' => 'Company Address'))           
           ->add('complaints', 'entity', array('class' => 'Info\ComplaintBundle\Entity\Complaint'))
           
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
           ->add('complaints')
       ;
   }
}