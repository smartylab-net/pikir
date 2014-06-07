<?php
/**
 * Created by PhpStorm.
 * User: bupychuk
 * Date: 01.06.14
 * Time: 18:44
 */

namespace Info\ComplaintBundle\Entity;
use Application\Sonata\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;


/**
 * Complaint
 *
 * @ORM\Table(name="manager_request")
 * @ORM\Entity()
 */

class ManagerRequest
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
     * @var User
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    public function __construct($user,$company)
    {
        $this->setUser($user);
        $this->setCompany($company);
    }
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param mixed $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return \Application\Sonata\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \Application\Sonata\UserBundle\Entity\User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

} 