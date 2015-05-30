<?php


namespace Application\Sonata\UserBundle\Twig;


use Application\Sonata\UserBundle\Entity\User;
use Symfony\Component\Security\Acl\Dbal\AclProvider;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Exception\NoAceFoundException;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\SecurityContext;

class UserExtension extends \Twig_Extension
{
    /**
     * @var SecurityContext
     */
    private $securityContext;
    /**
     * @var AccessDecisionManagerInterface
     */
    private $accessDecisionManager;

    public function __construct(SecurityContext $securityContext, AccessDecisionManagerInterface $accessDecisionManager) {

        $this->securityContext = $securityContext;
        $this->accessDecisionManager = $accessDecisionManager;
    }
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('role', array($this, 'getRole')),
        );
    }

    public function getRole(User $user)
    {
        if ($this->isGranted($user, 'ROLE_ADMIN')) {
            $role = 'Администратор';
        } elseif ($this->isGranted($user, 'ROLE_MODERATOR')) {
            $role = 'Модератор';
        } else {
            $role = 'Пользователь';
        }
        return $role;
    }


    private function isGranted(User $user, $role)
    {
        $token = new UsernamePasswordToken($user, 'none', 'none', $user->getRoles());
        return $this->accessDecisionManager
            ->decide($token, array($role));
    }

    public function getName()
    {
        return 'user_ext';
    }
}