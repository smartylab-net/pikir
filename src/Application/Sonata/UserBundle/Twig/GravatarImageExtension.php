<?php


namespace Application\Sonata\UserBundle\Twig;


use Application\Sonata\UserBundle\Entity\User;

class GravatarImageExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('gravatar_image', array($this, 'gravatarImage'), array('is_safe' => array('html'))),
        );
    }

    public function gravatarImage($user, $imageSize, $options = array())
    {
        /** @var $user User */
        $hash = ($user instanceof User)? md5($user->getEmail()) : "";
        $gravatarLink = "http://www.gravatar.com/avatar/$hash.png?d=mm&s=$imageSize";
        $attr = '';
        foreach ($options as $key => $value)
            $attr .= $key . '="' . $value . '"';
        return '<img src="' . $gravatarLink . '" ' . $attr . ' />';
    }

    public function getName()
    {
        return 'gravatar_image_ext';
    }
}