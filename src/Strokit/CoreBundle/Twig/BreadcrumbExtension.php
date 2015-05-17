<?php


namespace Strokit\CoreBundle\Twig;

use Symfony\Component\DependencyInjection\Container;

class BreadcrumbExtension extends \Twig_Extension {
        //TODO: создать сервис где будут храниться параметры и TwigExtension для показа в твиге.
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
            'bcm_breadcrumbs' => new \Twig_Function_Method($this, 'renderBreadcrumbs', array('is_safe' => array('html'))),
        );
    }

    public function renderBreadcrumbs()
    {
        $breadcrumbManager = $this->container->get('bcm_breadcrumb.manager');
        return $breadcrumbManager->render($this->params);
    }

    public function getName()
    {
        return 'bcm_breadcrumbs';
    }

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }
}