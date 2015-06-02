<?php


namespace Info\ComplaintBundle\Service;


use Doctrine\ORM\EntityManager;

class SiteNotificationService {

    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var WAMPClient
     */
    private $WAMPClient;

    public function __construct(EntityManager $entityManager, WAMPClient $WAMPClient) {
        $this->entityManager = $entityManager;
        $this->WAMPClient = $WAMPClient;
    }

    public function notifyComplaintAuthor($newComment)
    {
        //TODO: save to DB and send pub
    }
}