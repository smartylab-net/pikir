<?php

namespace Info\ReportBundle\Form;

use Info\ReportBundle\Entity\Report;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('report', 'choice', array(
                'choices'  => Report::$types,
                'required' => true,
                'expanded' => true,
                'multiple' => false
            ))
            ->add('save', 'submit', array('label' => 'Отправить', 'attr' => array('class' => 'btn btn-primary btn-xs pull-right')));
    }

    public function getName()
    {
        return 'report';
    }
} 