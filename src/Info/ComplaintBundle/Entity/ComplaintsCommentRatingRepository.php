<?php
 namespace Info\ComplaintBundle\Entity;

use Doctrine\ORM\EntityRepository; 

class ComplaintsCommentRatingRepository extends EntityRepository
{
	
	public function getIfComplaintVotedAndAuthorIsCurrentUser($id, $user)
    {
    	
	       return $this->getEntityManager()
            ->createQuery(
                "SELECT c
                FROM InfoComplaintBundle:ComplaintsCommentRating c
                WHERE 
                c.complaint = :id
                AND
                c.author = :user "
            )->setParameter('id', $id)
            ->setParameter('user', $user)
            ->getResult();
    }

    public function getIfComplaintVotedBefore($id)
    {
    	return $this->getEntityManager()
            ->createQuery(
                "SELECT c
                FROM InfoComplaintBundle:ComplaintsCommentRating c
                WHERE 
                c.complaint = :id
                "
            )->setParameter('id', $id)
            ->getResult();
    }

    public function getIfComplaintVotedAndAuthorIsCurrentAnonymousUser($id, $anonym, $ip)
    {
        
           return $this->getEntityManager()
            ->createQuery(
                "SELECT c
                FROM InfoComplaintBundle:ComplaintsCommentRating c
                WHERE 
                c.complaint = :id
                AND
                c.sessionCookie = :anonym 
                AND
                c.ip = :ip "
            )->setParameter('id', $id)
            ->setParameter('anonym', $anonym)
            ->setParameter('ip', $ip)
            ->getResult();
    }

    public function getIfCommentVotedAndAuthorIsCurrentUser($id, $user)
    {
        
           return $this->getEntityManager()
            ->createQuery(
                "SELECT c
                FROM InfoComplaintBundle:ComplaintsCommentRating c
                WHERE 
                c.comment = :id
                AND
                c.author = :user "
            )->setParameter('id', $id)
            ->setParameter('user', $user)
            ->getResult();
    }

    public function getIfCommentVotedBefore($id)
    {
        return $this->getEntityManager()
            ->createQuery(
                "SELECT c
                FROM InfoComplaintBundle:ComplaintsCommentRating c
                WHERE 
                c.comment = :id
                "
            )->setParameter('id', $id)
            ->getResult();
    }

    public function getIfCommentVotedAndAuthorIsCurrentAnonymousUser($id, $anonym, $ip)
    {
        
           return $this->getEntityManager()
            ->createQuery(
                "SELECT c
                FROM InfoComplaintBundle:ComplaintsCommentRating c
                WHERE 
                c.comment = :id
                AND
                c.sessionCookie = :anonym 
                AND
                c.ip = :ip "
            )->setParameter('id', $id)
            ->setParameter('anonym', $anonym)
            ->setParameter('ip', $ip)
            ->getResult();
    }
}
