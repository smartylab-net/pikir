<?php

namespace Info\ReportBundle\Entity;

use Application\Sonata\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Info\CommentBundle\Entity\Comment;
use Info\ComplaintBundle\Entity\Complaint;

/**
 * Report
 *
 * @ORM\Entity
 * @ORM\Table(name="report")
 */
class Report
{

    public static $types = array(
        'annoying'  => 'Это не приятно или не интересно',
        'filthy'    => 'Контент содержит неприличные слова',
        'answer'    => 'Я считаю, этому не место на этом сайте',
        'spam'      => 'Это спам'
    );

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var $report
     * @ORM\Column(name="report", type="string", nullable=false)
     */
    private $report;

    /**
     * @ORM\ManyToOne(targetEntity="Info\ComplaintBundle\Entity\Complaint", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="complaint_id", referencedColumnName="id")
     * })
     */

    private $complaint = null;

    /**
     * @ORM\ManyToOne(targetEntity="Info\CommentBundle\Entity\Comment", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="comment_id", referencedColumnName="id")
     * })
     */

    private $comment = null;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false )
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return String
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * @param String $report
     */
    public function setReport($report)
    {
        $this->report = $report;
    }

    /**
     * @return Complaint
     */
    public function getComplaint()
    {
        return $this->complaint;
    }

    /**
     * @param Complaint $complaint
     */
    public function setComplaint(Complaint $complaint)
    {
        $this->complaint = $complaint;
    }

    /**
     * @return Comment
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param Comment $comment
     */
    public function setComment(Comment $comment)
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
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }
}