<?php

namespace Strokit\CoreBundle\Service;

use BCM\BreadcrumbBundle\Service\BreadcrumbManager as BaseBreadcrumbManager;
use BCM\BreadcrumbBundle\Model\Item;
use Symfony\Component\Routing\RouterInterface;

class BreadcrumbManager extends BaseBreadcrumbManager
{
    public function addItem($routeName, $label, $params = array()) {

        $label = $this->generateLabel($label);

        $path = $this->router->generate($routeName, $params);
        $item = new Item();
        $item->setLabel($label);
        $item->setPath($path);

        $this->items->add($item);
    }

    public function addBreadcrumbsForRoute($routeName, $params) {
        $this->parameters = $params;
        $route = $this->router->getRouteCollection()->get($routeName);
        $this->buildRecursivelyItemsByRoute($route, $routeName);
    }
}
