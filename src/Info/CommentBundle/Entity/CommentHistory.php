<?php

namespace Info\CommentBundle\Entity;

use Application\Sonata\UserBundle\Entity\User;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Comment
 *
 * @ORM\Table(name="comment_history")
 * @ORM\Entity(repositoryClass="Info\CommentBundle\Repository\CommentHistoryRepository")
 */
class CommentHistory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var $comment
     * @ORM\Column(name="comment", type="text", nullable=false)
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity="Info\CommentBundle\Entity\Comment", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="targetComment_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $targetComment;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */

    private $user;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */

    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return Comment
     */
    public function getTargetComment()
    {
        return $this->targetComment;
    }

    /**
     * @param Comment $targetComment
     */
    public function setTargetComment(Comment $targetComment)
    {
        $this->targetComment = $targetComment;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    function __toString()
    {
        return $this->getId() . '';
    }
}
