<?php


namespace Info\CommentBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Info\CommentBundle\Entity\Comment;

class CommentHistoryRepository extends EntityRepository {

    public function getHistory(Comment $comment) {
        return $this->findBy(array('targetComment'=>$comment), array('createdAt'=>'desc'));
    }

}