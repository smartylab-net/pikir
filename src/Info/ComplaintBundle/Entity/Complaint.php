<?php

namespace Info\ComplaintBundle\Entity;

use Application\Sonata\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Complaint
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Info\ComplaintBundle\Entity\ComplaintRepository")
 */

class Complaint
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
     * @ORM\ManyToOne(targetEntity="Info\ComplaintBundle\Entity\Company", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     * })
     */
    private $company;

    /**
     * @ORM\OneToMany(targetEntity="Info\CommentBundle\Entity\Comment", mappedBy="complaint", cascade={"persist", "remove" }, orphanRemoval=true)
     */
    private $comments;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     * })
     */
    private $author;

    /**
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     * @Assert\NotNull()
     */
    private $text;

    /**
     * @var integer
     *
     * @ORM\Column(name="rating", type="integer")
     * @Assert\NotNull()
     */
    private $rating = 0;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media",cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="someFile", referencedColumnName="id")
     * })
     */
    private $someFile;

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
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set company
     *
     * @param \Info\ComplaintBundle\Entity\Company $company
     * @return Complaint
     */
    public function setCompany(\Info\ComplaintBundle\Entity\Company $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }


    /**
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param int $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    public function __toString()
    {
        return $this->title;
    }

    public function __construct(){
        $this->created = new DateTime();
    }


    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Complaint
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param mixed $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
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
     * @return mixed
     */
    public function getSomeFile()
    {
        return $this->someFile;
    }

    /**
     * @param mixed $someFile
     */
    public function setSomeFile($someFile)
    {
        $this->someFile = $someFile;
    }
}
