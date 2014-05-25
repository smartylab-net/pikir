<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 02.01.14
 * Time: 10:50
 */

namespace Strokit\CoreBundle\DBAL;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

abstract class EnumType extends Type
{
    protected $name;
    protected $values = array();

    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $values = array_map(function($val) { return "'".$val."'"; }, $this->values);

        return "ENUM(".implode(", ", $values).") COMMENT '(DC2Type:".$this->name.")'";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value >= count($this->values)) {
            throw new \InvalidArgumentException("Invalid '".$this->name."' value.");
        }
        return $this->values[$value];
    }

    public static function getArray()
    {
        return array();
    }

    public function getName()
    {
        return $this->name;
    }
}