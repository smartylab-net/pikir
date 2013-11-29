<?php

namespace Info\FAQBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FaqComments
 *
 * @ORM\Table(name="faq_comments")
 * @ORM\Entity
 */
class FaqComments
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var boolean $active
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    private $active;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

    /**
     * @var \FaqQuestionsAnswers
     *
     * @ORM\ManyToOne(targetEntity="FaqQuestionsAnswers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="questions_id", referencedColumnName="id")
     * })
     */
    private $questions;



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
     * Set content
     *
     * @param string $content
     * @return FaqComments
     */
    public function setContent($content)
    {
        $this->content= $content;
    
        return $this;
    }

    /**
     * Get comContents
     *
     * @return string 
     */
    public function getContents()
    {
        return $this->content;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return FaqComments
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set questions
     *
     * @param \Info\FAQBundle\Entity\FaqQuestionsAnswers $questions
     * @return FaqComments
     */
    public function setQuestions(\Info\FAQBundle\Entity\FaqQuestionsAnswers $questions = null)
    {
        $this->questions = $questions;
    
        return $this;
    }

    /**
     * Get questions
     *
     * @return \Info\FAQBundle\Entity\FaqQuestionsAnswers 
     */
    public function getQuestions()
    {
        return $this->questions;
    }
}