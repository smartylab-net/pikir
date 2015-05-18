<?php


namespace Info\CommentBundle\Entity;


use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class CommentRepository extends NestedTreeRepository {

    public function getRootCommentsByComplaint($complaint)
    {
        return $this->findBy(array('complaint'=>$complaint, 'lft'=>1));
    }

    public function countByComplaint($complaint)
    {
        $rootComments = $this->getRootCommentsByComplaint($complaint);
        $commentCount = count($rootComments);
        foreach ($rootComments as $comment) {
            $commentCount +=$this->childCount($comment);
        }
        return $commentCount;
    }
}