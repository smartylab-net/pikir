<?php
/**
 * Created by PhpStorm.
 * User: bupychuk
 * Date: 24.05.14
 * Time: 18:11
 */

namespace Info\ComplaintBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CompanyType extends AbstractType{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('logo', 'sonata_media_type', array(
                'provider' => 'sonata.media.provider.image',
                'context'  => 'company',
                'required' => false,
                'label'=>'Логотип',
                'label_attr'=>array('class'=>'col-sm-2 control-label')
            ))
            ->add('name','text',array(
                'required'=>true,
                'label'=>'Название',
                'label_attr'=>array('class'=>'col-sm-2 control-label'),
                'attr'=>array('class'=>'form-control')
            ))
            ->add('address','text',array(
                'label'=>'Адрес',
                'label_attr'=>array('class'=>'col-sm-2 control-label'),
                'attr'=>array('class'=>'form-control')
            ))
            ->add('annotation','textarea',array(
                'label'=>'Описание',
                'label_attr'=>array('class'=>'col-sm-2 control-label'),
                'attr'=>array('class'=>'form-control')
            ))
            ->add('submit','submit',array(
                'label'=>'Сохранить',
                'attr'=>array('class'=>'btn btn-primary')
            ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'company_form';
    }
}