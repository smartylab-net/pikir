<?php
namespace Info\PageBundle\DBAL;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Strokit\CoreBundle\DBAL\EnumType;

class PageTypeEnum extends EnumType
{
    protected $name = 'pagetype_enum';
    protected $values = array('top','bottom');
    public static function getArray()
    {
        return array('top'=>'Верхнее меню','bottom'=>'Нижнее меню');
    }
}