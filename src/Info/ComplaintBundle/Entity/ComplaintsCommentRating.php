<?php

namespace Info\ComplaintBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ComplaintsCommentRating
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Info\ComplaintBundle\Entity\ComplaintsCommentRatingRepository")
 */
class ComplaintsCommentRating
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
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", nullable=true)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="cookie_session", type="string", nullable=true)
     */
    private $sessionCookie;

    /**
     * @ORM\ManyToOne(targetEntity="Info\ComplaintBundle\Entity\Complaint", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="complaint_id", referencedColumnName="id")
     * })
     */
    private $complaint;

    /**
     * @ORM\ManyToOne(targetEntity="Info\CommentBundle\Entity\Comment", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="comment_id", referencedColumnName="id")
     * })
     */
    private $comment;

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
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return string
     */
    public function getSessionCookie()
    {
        return $this->sessionCookie;
    }

    /**
     * @param string $sessionCookie
     */
    public function setSessionCookie($sessionCookie)
    {
        $this->sessionCookie = $sessionCookie;
    }

    /**
     * @return mixed
     */
    public function getComplaint()
    {
        return $this->complaint;
    }

    /**
     * Set complaint
     *
     * @param \Info\ComplaintBundle\Entity\Complaint $complaint
     * @return ComplaintCommentRating
     */
    public function setComplaint(\Info\ComplaintBundle\Entity\Complaint $complaint = null)
    {
        $this->complaint = $complaint;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set comment
     *
     * @param \Info\CommentBundle\Entity\Comment $comment
     * @return ComplaintCommentRating
     */
    public function setComment(\Info\CommentBundle\Entity\Comment $comment = null)
    {
        $this->comment = $comment;

        return $this;
    }
}
