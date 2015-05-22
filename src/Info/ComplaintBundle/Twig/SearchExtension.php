<?php


namespace Info\ComplaintBundle\Twig;

use Info\ComplaintBundle\Form\SearchType;
use Symfony\Component\DependencyInjection\Container;

class SearchExtension extends \Twig_Extension {

    private $params = array();
    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container) {

        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            'search_form' => new \Twig_Function_Method($this, 'search', array('is_safe' => array('html'))),
            'error_search_form' => new \Twig_Function_Method($this, 'errorSearch', array('is_safe' => array('html'))),
        );
    }

    public function search()
    {
        $form = $this->container->get('form.factory')->create(new SearchType());

        $templating = $this->container->get('templating');
        return $templating->render('InfoComplaintBundle:Search:form.html.twig', array('form' => $form->createView()));
    }

    public function errorSearch()
    {
        $form = $this->container->get('form.factory')->create(new SearchType());

        $templating = $this->container->get('templating');
        return $templating->render('InfoComplaintBundle:Search:error_search.html.twig', array('form' => $form->createView()));
    }

    public function getName()
    {
        return 'search';
    }

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }
}