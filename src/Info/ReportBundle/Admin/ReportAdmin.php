<?php
namespace Info\ReportBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ReportAdmin extends Admin
{
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'read',
    );

    protected $baseRouteName = 'admin_report';

   // Fields to be shown on filter forms
   protected function configureDatagridFilters(DatagridMapper $datagridMapper)
   {
       $datagridMapper
           ->add('user')
           ->add('read', null, array('label'=>'Прочитан'))
       ;
   }

   // Fields to be shown on lists
   protected function configureListFields(ListMapper $listMapper)
   {
       $listMapper
           ->add('user')
           ->add('complaint')
           ->add('comment')
           ->add('report')
           ->add('read',null,array('label'=>'Прочитан','editable'=>true))
           ->add('createdAt')
       ;
   }
}