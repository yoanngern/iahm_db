<?php

namespace iahm\UserBundle\Entity;

use FOS\UserBundle\Entity\User as UserBase;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="iahm_user")
 * @ORM\Entity(repositoryClass="iahm\UserBundle\Entity\UserRepository")
 */
class User extends UserBase
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        // your own logic
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
}
