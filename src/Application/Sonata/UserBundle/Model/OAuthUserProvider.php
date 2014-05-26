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
     * @var mixed
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
        $nickname = $response->getNickname();
//        $realname = $response->getRealName();
        $email    = $response->getEmail();

        $resourceOwnerName = $response->getResourceOwner()->getName();

        if (!is_null($email)){
            $user = $this->repository->findOneBy(
                array('resource' => $resourceOwnerName, 'email' => $email)
            );
        }else{
            $user = $this->repository->findOneBy(
                array('resource' => $resourceOwnerName, 'username' => $nickname)
            );
        }

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
            }

            $password = $this->generate_password(6);
            $user->setUsername($username);
            $user->setResource($resourceOwnerName);
            $user->setEmail($email);
            $user->setLastLogin(new \DateTime());
            $user->setPlainPassword($password);
            $this->container->get('fos_user.user_manager')->updateUser($user);

            $message = \Swift_Message::newInstance()
                ->setSubject('Сайт жалоб')
                ->setFrom('noreply@strokit.net')
                ->setTo($email)
                ->setBody('<p>Ваш пароль на сайт: <b>'.$password.'</b></p>', 'text/html')
            ;
            $this->container->get('mailer')->send($message);
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
}