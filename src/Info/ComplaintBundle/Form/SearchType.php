<?php
/**
 * Created by PhpStorm.
 * User: Aykut
 * Date: 5/24/14
 * Time: 1:27 PM
 */
// src/Acme/TaskBundle/Controller/DefaultController.php
namespace Info\ComplaintBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $searchBuilder, array $options)
    {
        $searchBuilder
            ->add('name', 'text', array('required'=>true))
            ->add('search', 'submit', array('label'=>'Поиск'));
    }

    public function getName()
    {
        return 'poisk';
    }
}