<?php


namespace Info\CommentBundle\Twig;

use Doctrine\ORM\EntityManager;
use Info\CommentBundle\Entity\CommentRepository;
use Info\ComplaintBundle\Entity\Complaint;

class CommentCountExtension extends \Twig_Extension {

    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var CommentRepository
     */
    private $commentRepository;

    public function __construct($entityManager) {

        $this->entityManager = $entityManager;
        $this->commentRepository = $this->entityManager->getRepository('InfoCommentBundle:Comment');
    }


    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('comment_count', array($this, 'commentCount')),
        );
    }

    public function commentCount(Complaint $complaint)
    {
        return $this->commentRepository->countByComplaint($complaint);
    }

    public function getName()
    {
        return 'comment_count';
    }
}