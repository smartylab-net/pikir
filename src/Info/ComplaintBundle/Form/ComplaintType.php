<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 29.03.14
 * Time: 18:00
 */

namespace Info\ComplaintBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class ComplaintType extends AbstractType {

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return "Complaint";
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('company','entity', array('class' => 'Info\ComplaintBundle\Entity\Company',
                'empty_data'  => null,
                'empty_value' => '',
                'required'=>false))
            ->add('text','textarea', array('required'=>true))
            ->add('rating','hidden',array('required'=>true))
            ->add('submit','submit', array('label' => 'Отправить'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class'=>'Info\ComplaintBundle\Entity\Complaint'));
    }
}