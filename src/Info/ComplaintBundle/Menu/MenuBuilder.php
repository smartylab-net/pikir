<?php
namespace Info\ComplaintBundle\Menu;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
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
     * @param $em
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
        $menu->setChildrenAttribute('class','gui-controls gui-controls-tree nav');
        $menu->addChild('Home',
            array(
                'route' => 'info_complaint_homepage',
                'label'=>'Главная',
                'extras' => array(
                    'icon' => 'md md-home'
                )
            ));
        $companiesMenuItem = $menu->addChild('Companies',
            array(
                'route' => 'info_complaint_category',
                'label'=>'Компании',
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

        $this->getCategoryMenu($this->em, $companiesMenuItem);
        $this->getPagesMenu($menu, 'top');
        return $menu;
    }


    /**
     * создает боковое меню каталога
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return mixed
     */


    public function createCategoryLeftMenu(Request $request){

        $menu = $this->factory->createItem('category_side_menu');
        $menu->setCurrentUri($request->getRequestUri());
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
            $cat = $menu->addChild($category->getName(), array('route' => 'info_complaint_category', 'routeParameters' => array('categorySlug' => $category->getSlug())))->setDisplayChildren(true);
            $subcategory = $cm->findBy(array('parent' => $category->getId(), 'enabled' => true));
            foreach ($subcategory as $child) {
                if ($child->getEnabled())
                    $cat->addChild($child->getName(), array('route' => 'info_complaint_category', 'routeParameters' => array('categorySlug' => $child->getSlug())));
            }
        }
    }

    public function getPagesMenu(ItemInterface $menu)
    {
        /** @var ObjectRepository $pagesRepository */
        $pagesRepository=$this->em->getRepository('InfoPageBundle:Pages');
        $pages = $pagesRepository->findAll();

        foreach ($pages as $page)
        {
            /** @var Pages $page */
            $menu->addChild($page->getTitle(),
                array(
                    'route' => 'info_page',
                    'routeParameters' => array('url' => $page->getUrl()),
                    'extras' => array(
                        'icon' => $page->getIconClass()
                    )
                ));
        }

    }
}