<?php
namespace Info\ComplaintBundle\Menu;

use Doctrine\ORM\EntityManager;
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
     * @var \Info\PageBundle\Menu\MenuBuilder
     */
    private $pageMenuBuilder;

    /**
     * @param FactoryInterface $factory
     * @param $em
     * @param $pageMenuBuilder
     */
    public function __construct(FactoryInterface $factory,$em, $pageMenuBuilder)
    {
        $this->factory = $factory;
        $this->em = $em;
        $this->pageMenuBuilder = $pageMenuBuilder;
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
        $this->pageMenuBuilder->getPagesMenu($menu, 'top');
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
            $cat = $menu->addChild($category->getName(), array('route' => 'info_complaint_category', 'routeParameters' => array('cId' => $category->getId())))->setDisplayChildren(true);
            $subcategory = $cm->findBy(array('parent' => $category->getId(), 'enabled' => true));
            foreach ($subcategory as $child) {
                if ($child->getEnabled())
                    $cat->addChild($child->getName(), array('route' => 'info_complaint_category', 'routeParameters' => array('cId' => $child->getId())));
            }
        }
    }
}