<?php
/**
 * Created by PhpStorm.
 * User: bupychuk
 * Date: 31.05.14
 * Time: 21:10
 */

namespace Application\Sonata\UserBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class NotificationType extends AbstractType{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('emailOnNewComplaint',null,array('label'=>'Уведомлять по Email при получении новых отзывов'))
                ->add('emailOnNewComment',null,array('label'=>'Уведомлять по Email при получении нового комментария'))
                ->add('emailOnReplyToComment',null,array('label'=>'Уведомлять по Email при ответе на мой комментарий'))
                ->add('emailOnReport',null,array('label'=>'Уведомлять по Email при получении жалоб'))
                ->add('notifyOnNewComplaint',null,array('label'=>'Уведомлять на сайте при получении новых отзывов'))
                ->add('notifyOnNewComment',null,array('label'=>'Уведомлять на сайте при получении нового комментария'))
                ->add('notifyOnReplyToComment',null,array('label'=>'Уведомлять на сайте при ответе на мой комментарий'))
                ->add('submit','submit',array('label'=>'Сохранить'));
    }
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'notification_type';
    }
}