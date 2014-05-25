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
                'attr' => array(
                    'placeholder' => 'Комментарий'
                )
            ))
            ->add('save', 'submit', array('label'=>'Сохранить'))
        ;
    }

    public function getName()
    {
        return 'comment';
    }
} 