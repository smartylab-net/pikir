<?php
namespace Info\ComplaintBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerAware;
use Info\ComplaintBundle\Entity;

class MenuBuilder extends ContainerAware
{
    private $factory;
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory,$em)
    {
        $this->factory = $factory;
        $this->em = $em;
    }

    /**
     * создает боковое меню каталога
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return mixed
     */


    public function createCategoryLeftMenu(Request $request){

        $menu = $this->factory->createItem('category_side_menu');
        $menu->setCurrentUri($request->getRequestUri());
        $this->getCategoryMenu($this->em, $menu);
        return $menu;
    }

    /**
     * @param $em
     * @param $menu
     */
    public function getCategoryMenu($em, $menu)
    {
        $cm = $em->getRepository("ApplicationSonataClassificationBundle:Category");

        $categories = $cm->findBy(array('enabled' => 1, 'parent' => null));

        foreach ($categories as $category) {
            $cat = $menu->addChild($category->getName(), array('route' => 'info_complaint_category', 'routeParameters' => array('cId' => $category->getId())))->setDisplayChildren(true);
            $subcategory = $cm->findBy(array('parent' => $category->getId(), 'enabled' => true));
            foreach ($subcategory as $child) {
                if ($child->getEnabled())
                    $cat->addChild($child->getName(), array('route' => 'info_complaint_category', 'routeParameters' => array('cId' => $child->getId())));
            }
        }
    }
}