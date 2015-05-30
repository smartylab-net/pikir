<?php

namespace Info\CommentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('comment', 'textarea', array(
                'label' => 'Комментарий',
                'attr' => array(
                    'rows' => 1
                )
            ))
            ->add('save', 'submit', array('label' => 'Отправить', 'attr' => array('class' => 'btn btn-primary btn-xs pull-right')));
    }

    public function getName()
    {
        return 'comment';
    }
} 