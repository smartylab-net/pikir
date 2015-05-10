<?php
namespace Info\PageBundle\Menu;

use Doctrine\ORM\EntityManager;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerAware;

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
     * создает боковое меню
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return mixed
     */

    public function createBottomMenu(Request $request)
    {
        $menu = $this->factory->createItem('bottom');
        $menu->setCurrentUri($request->getRequestUri());

        $menu->setChildrenAttribute('id','main-menu');
        $menu->setChildrenAttribute('class','gui-controls');

        $this->getPagesMenu($menu, 'bottom');
        return $menu;
    }

    public function getPagesMenu(ItemInterface $menu,$position)
    {
        $pagesRepository=$this->em->getRepository('InfoPageBundle:Pages');
        $pages = $pagesRepository->findBy(array('position'=>$position));

        foreach ($pages as $page)
        {
            $menu->addChild($page->getTitle(), array('route' => 'nurix_create_pages', 'routeParameters' => array('url' => $page->getUrl())));
        }

    }
}