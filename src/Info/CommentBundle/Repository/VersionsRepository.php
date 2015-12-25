<?php


namespace Info\CommentBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Info\CommentBundle\Entity\Comment;
use Info\ComplaintBundle\Entity\Complaint;

class VersionsRepository extends EntityRepository {

    public function getHistoryComment(Comment $comment) {
        return $this->findBy(array('targetComment'=>$comment), array('createdAt'=>'desc'));
    }

    public function getHistoryComplaint(Complaint $complaint) {
        return $this->findBy(array('targetComplaint'=>$complaint), array('createdAt'=>'desc'));
    }

}