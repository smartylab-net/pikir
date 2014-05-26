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
     * создает главное меню
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return mixed
     */

    public function createMainMenu(Request $request)
    {
        $menu = $this->factory->createItem('main');
        $menu->setCurrentUri($request->getRequestUri());
        $menu->setChildrenAttribute('class','nav navbar-nav');
//        $menu->addChild('Home', array('route' => 'info_complaint_homepage','label'=>'Главная'));
        $menu->addChild('Companies', array('route' => 'info_complaint_category','label'=>'Все компании'));
        $menu->addChild('Complaint', array('route' => 'info_complaint_create','label'=>'Добавить отзыв'));
//        $menu->addChild('Catalog', array('route' => 'nurix_goods_get_catalog','routeParameters'=>array('cid'=>null),'label'=>'Каталог'));

//        $menu->addChild('available', array('route' => 'nurix_catalog_get_available','label'=>'В наличии'));

        $this->getPagesMenu($menu, 'top');
        return $menu;
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

        $menu->setChildrenAttribute('class','nav navbar-nav');

        $this->getPagesMenu($menu, 'bottom');
        return $menu;
    }

    private function getPagesMenu(ItemInterface $menu,$position)
    {
        $pagesRepository=$this->em->getRepository('InfoPageBundle:Pages');
        $pages = $pagesRepository->findBy(array('position'=>$position));

        foreach ($pages as $page)
        {
            $menu->addChild($page->getTitle(), array('route' => 'nurix_create_pages', 'routeParameters' => array('url' => $page->getUrl())));
        }

    }
}