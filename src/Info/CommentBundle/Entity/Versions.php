<?php

namespace Info\CommentBundle\Entity;

use Application\Sonata\UserBundle\Entity\User;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Info\ComplaintBundle\Entity\Complaint;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Comment
 *
 * @ORM\Table(name="versions")
 * @ORM\Entity(repositoryClass="Info\CommentBundle\Repository\VersionsRepository")
 */
class Versions
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
     * @var $version
     * @ORM\Column(name="version", type="text", nullable=false)
     */
    private $version;

    /**
     * @ORM\ManyToOne(targetEntity="Info\CommentBundle\Entity\Comment", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="targetComment_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $targetComment;

    /**
     * @ORM\ManyToOne(targetEntity="Info\ComplaintBundle\Entity\Complaint", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="targetComplaint_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $targetComplaint;

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
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
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
     * @return Complaint
     */
    public function getTargetComplaint()
    {
        return $this->targetComplaint;
    }

    /**
     * @param Complaint $targetComplaint
     */
    public function setTargetComplaint(Complaint $targetComplaint)
    {
        $this->targetComplaint = $targetComplaint;
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
