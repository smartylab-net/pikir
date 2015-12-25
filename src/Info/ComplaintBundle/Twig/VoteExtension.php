<?php


namespace Info\ComplaintBundle\Twig;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;

class VoteExtension extends \Twig_Extension {

    /**
     * @var Container
     */
    private $container;
    private $complaintsCommentRatingRep;

    public function __construct(Container $container, EntityManager $entityManager) {
        $this->container = $container;
        $this->complaintsCommentRatingRep = $entityManager->getRepository('InfoComplaintBundle:ComplaintsCommentRating');
    }

    public function getFilters()
    {
        return array(
             new \Twig_SimpleFilter('is_voted', array($this, 'isVoted'))
        );
    }

    public function isVoted($element, $type, $voteType)
    {
        $request = $this->container->get('request');
        $cookie = $request->cookies->get('anonymous-vote');
        $ip = $request->getClientIp();
        $id = $element->getId();
        $user = $request->getUser();
        if ($user) {
            $vote = $this->complaintsCommentRatingRep->findOneBy(array('elementId'=> $id, 'type' => $type, 'author' => $user, 'vote' => $voteType));
        } else {
            $vote = $this->complaintsCommentRatingRep->findOneBy(array('elementId' => $id, 'sessionCookie' => $cookie, 'ip' => $ip, 'vote' => $voteType));
        }

        return $vote !== null;
    }

    public function getName()
    {
        return 'vote';
    }
}