<?php


namespace Info\CommentBundle\Handler;


use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Info\CommentBundle\Entity\Comment;
use Info\CommentBundle\Entity\CommentHistory;
use Symfony\Component\DependencyInjection\ContainerInterface;

class VersionHandler implements EventSubscriber
{
    protected $things = [];

    /**
     * @var ContainerInterface $container
     */
    protected $container;

    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function getSubscribedEvents()
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            return [
                'preUpdate',
                'postFlush'
            ];
        }

        return [];
    }

    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();

        //Updates
        if ($entity instanceof Comment) {
            $oldVersion = $eventArgs->getOldValue('comment');
            if (trim($oldVersion) != trim($eventArgs->getNewValue('comment'))) {
                $version = new CommentHistory();
                $version->setComment($oldVersion);
                $version->setTargetComment($entity);
                $version->setUser($this->getUser());

                $this->things[] = $version;
            }

        }
    }

    public function postFlush(PostFlushEventArgs $event)
    {
        if(!empty($this->things)) {

            $em = $event->getEntityManager();

            foreach ($this->things as $thing) {
                $em->persist($thing);
            }

            $this->things = [];
            $em->flush();
        }
    }

    public function getUser()
    {
        if (!$this->container->has('security.context')) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        if (null === $token = $this->container->get('security.context')->getToken()) {
            return null;
        }

        if (!is_object($user = $token->getUser())) {
            return null;
        }

        return $user;
    }
}