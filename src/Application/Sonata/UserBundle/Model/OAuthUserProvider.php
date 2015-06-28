<?php
/**
 * Created by PhpStorm.
 * User: junus
 * Date: 5/24/14
 * Time: 1:28 PM
 */

namespace Application\Sonata\UserBundle\Model;

use Application\Sonata\UserBundle\Entity\User;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface,
    HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface,
    Symfony\Component\Security\Core\User\UserProviderInterface,
    HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider as BaseOAuthUserProvider,
    Doctrine\Common\Persistence\ManagerRegistry,
    Symfony\Component\Security\Core\Exception\UsernameNotFoundException,
    Symfony\Component\Security\Core\User\UserInterface,
    Symfony\Component\DependencyInjection\ContainerInterface;

class OAuthUserProvider implements UserProviderInterface, OAuthAwareUserProviderInterface {
    /**
     * @var mixed
     */
    protected $em;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var ContainerInterface
     *
     * @api
     */
    private $container;

    /**
     * @var UserRepository $repository
     */
    protected $repository;

    public function __construct(ContainerInterface $containerInterface, ManagerRegistry $registry, $className) {
        $this->em = $registry->getManager();
        $this->repository = $this->em->getRepository($className);
        $this->className = $className;
        $this->container = $containerInterface;
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response) {
        $username = $response->getUsername();
        $email    = $response->getEmail();

        $resourceOwnerName = $response->getResourceOwner()->getName();

        $user = $this->findUser($response, $email, $resourceOwnerName, $username);

        if (null === $user) {
            /** @var $user User */
            $user = new $this->className();
            //TODO сделать нормально через интерфейсы, но это когда у нас будет больше провайдеров
            if ($resourceOwnerName == 'facebook')
            {
                $responseData = $response->getResponse();
                $user->setFirstname($responseData['first_name']);
                $user->setLastname($responseData['last_name']);
                $user->setFacebookName($response->getRealName());
                $user->setFacebookUid($username);
            }

            $password = $this->generate_password(6);
            $user->setUsername($username);
            $user->setResource($resourceOwnerName);
            $user->setEmail($email);
            $user->setLastLogin(new \DateTime());
            $user->setPlainPassword($password);
            $this->save($user);
        }

        return $user;
    }

    public function loadUserByUsername($username) {

        $user = $this->repository->findOneBy(array('username' => $username));
        if (!$user) {
            throw new UsernameNotFoundException(sprintf("User '%s' not found.", $username));
        }

        return $user;
    }

    public function refreshUser(UserInterface $user) {
        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === $this->class || is_subclass_of($class, $this->class);
    }

    protected function generate_password($number)
    {
        $arr = array('a','b','c','d','e','f',
            'g','h','i','j','k','l',
            'm','n','o','p','r','s',
            't','u','v','x','y','z',
            'A','B','C','D','E','F',
            'G','H','I','J','K','L',
            'M','N','O','P','R','S',
            'T','U','V','X','Y','Z',
            '1','2','3','4','5','6',
            '7','8','9','0');
        // Генерируем пароль
        $pass = "";
        for($i = 0; $i < $number; $i++)
        {
            // Вычисляем случайный индекс массива
            $index = rand(0, count($arr) - 1);
            $pass .= $arr[$index];
        }
        return $pass;
    }

    private function connect(User $user,UserResponseInterface $response)
    {
        $resourceOwnerName = $response->getResourceOwner()->getName();
        if ($resourceOwnerName == 'facebook') {
            $user->setFacebookUid($response->getUsername());
            $user->setFacebookName($response->getRealName());
            $user->setResource($resourceOwnerName);
            $this->save($user);
        }
    }

    /**
     * @param $user
     */
    protected function save($user)
    {
        $this->container->get('fos_user.user_manager')->updateUser($user);
    }

    /**
     * @param UserResponseInterface $response
     * @param $email
     * @param $resourceOwnerName
     * @param $username
     * @return null|object
     */
    protected function findUser(UserResponseInterface $response, $email, $resourceOwnerName, $username)
    {
        $user = null;
        if (!is_null($email)) {
            $user = $this->repository->findOneBy(
                array('email' => $email)
            );
            if ($user != null) {
                $this->connect($user, $response);
            }
        }
        if ($user == null && $resourceOwnerName == 'facebook') {
            $user = $this->repository->findOneBy(
                array('facebookUid' => $username)
            );
            if ($user != null && $email != $user->getEmail()) {
                $user->setEmail($email);
            }
        } else if ($user == null) {
            $user = $this->repository->findOneBy(
                array('resource' => $resourceOwnerName, 'username' => $username)
            );
            return $user;
        }
        return $user;
    }
}