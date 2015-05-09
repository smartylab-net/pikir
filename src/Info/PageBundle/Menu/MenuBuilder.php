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
        $menu->setChildrenAttribute('id','main-menu');
        $menu->setChildrenAttribute('class','gui-controls');
        $menu->addChild('Companies',
            array(
                'route' => 'info_complaint_category',
                'label'=>'Все компании',
                'extras' => array(
                    'icon' => 'md md-domain'
                )
            ));
        $menu->addChild('Create_Company',
            array(
                'route' => 'info_company_create',
                'label'=>'Добавить компанию',
                'extras' => array(
                    'icon' => 'md md-work'
                )
            ));
        $menu->addChild('Complaint',
            array(
                'route' => 'info_complaint_create',
                'label'=>'Добавить отзыв',
                'extras' => array(
                    'icon' => 'md md-create'
                )
            ));

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