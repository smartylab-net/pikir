<?php
namespace Nurix\CatalogBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerAware;
use Nurix\CatalogBundle\Model\CatalogModel;
use Nurix\CatalogBundle\Entity;

class MenuBuilder extends ContainerAware
{
    private $factory;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * создает боковое меню каталога
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return mixed
     */


    public function createCatalogLeftMenu(Request $request,$em){

        $menu = $this->factory->createItem('catalog_side_menu');
        $menu->setCurrentUri($request->getRequestUri());
        $menu->setChildrenAttribute('class','unstyled');
        $this->getCatalogMenu($em, $menu);
        return $menu;
    }

    /**
     * @param $em
     * @param $menu
     */
    public function getCatalogMenu($em, $menu)
    {
        $cm = $em->getRepository("CatalogBundle:Catalog");

        $catalog = $cm->getAll(array('active' => 1, 'parent' => null));

        foreach ($catalog as $category) {
            $cat = $menu->addChild($category->getCname(), array('route' => 'nurix_goods_get_catalog', 'routeParameters' => array('cid' => $category->getId())))->setDisplayChildren(true);
            foreach ($category->getChildren() as $child) {
                if ($child->getActive())
                    $cat->addChild($child->getCname(), array('route' => 'nurix_goods_get_catalog', 'routeParameters' => array('cid' => $child->getId())));
            }
        }
    }
}