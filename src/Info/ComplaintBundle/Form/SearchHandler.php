<?php
/**
 * Created by PhpStorm.
 * User: Aykut
 * Date: 5/24/14
 * Time: 2:20 PM
 */
namespace Info\ComplaintBundle\Controller;

use Symfony\Component\Form\Form;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class SearchHandler
{
    protected $form;
    protected $request;
    protected $em;

    public function __construct(Form $form, Request $request, EntityManager $em){
        $this->form = $form;
        $this->request = $request;
        $this->em = $em;
    }

    public function process(){
        if( $this->request->getMethod() == 'POST' ){
            $this->form->bind($this->request);
            if ($this->form->isValid()) {
                return true;
            }
            else {
                return false;
            }
        }
        return false;
    }
}