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
            ))
            ->add('name','text',array(
                'required'=>true,
                'label'=>'Название',
            ))
            ->add('category','entity',array(
                'label' => 'Категория',
                'class' => 'Application\Sonata\ClassificationBundle\Entity\Category',
            ))
            ->add('annotation','textarea',array(
                'label'=>'Описание',
                'attr'=>array('rows' => 1)
            ))
            ->add('address','text',array(
                'label'=>'Адрес',
                'required' => false,
            ))
            ->add('phone','text',array(
                'label'=>'Номер телефона',
                'required' => false,
            ))
            ->add('site','text',array(
                'label'=>'Сайт',
                'required' => false,
                'attr'=>array('data-rule-url' => 'true')
            ))
            ->add('facebook','text',array(
                'label'=>'Страница в Facebook',
                'required' => false,
                'attr'=>array('data-rule-url' => 'true')
            ))
            ->add('twitter','text',array(
                'label'=>'Страница в Twitter',
                'required' => false,
                'attr'=>array('data-rule-url' => 'true')
            ))
            ->add('instagram','text',array(
                'label'=>'Страница в Instagram',
                'required' => false,
                'attr'=>array('data-rule-url' => 'true', 'data-rule-regex' => 'http(s?):\/\/(www\.)?(instagram).com(\/(.*))?')
            ))
            ->add('submit','submit',array(
                'label'=>'Сохранить'
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