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


class ComplaintType extends abstractType {

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        // TODO: Implement getName() method.
        return "Complaint";
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('company','entity', array('class' => 'Info\ComplaintBundle\Entity\Company'))//'text',array('label'=>'Название компании')
            ->add('title','text',array('label'=>'Тема'))
            ->add('text','textarea',array('label'=>'Текст'))
            ->add('rating','hidden')
            ->add('submit','submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class'=>'Info\ComplaintBundle\Entity\Complaint'));
    }
}