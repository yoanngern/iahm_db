<?php

namespace iahm\ContactBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;

/**
 * Comment
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="iahm\ContactBundle\Entity\CommentRepository")
 * @ExclusionPolicy("all")
 */
class Comment
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
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     * @Expose
     */
    private $text;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdDate", type="datetime")
     */
    private $createdDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modifiedDate", type="datetime")
     */
    private $modifiedDate;


    /**
     * @ORM\OneToOne(targetEntity="iahm\ContactBundle\Entity\Person", inversedBy="comment")
     */
    private $person;

    /**
     * @ORM\OneToOne(targetEntity="iahm\ContactBundle\Entity\Family", inversedBy="comment")
     */
    private $family;

    /**
     * @ORM\OneToOne(targetEntity="iahm\ContactBundle\Entity\Donation", inversedBy="comment")
     */
    private $donation;


    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preSave()
    {
        $this->modifiedDate = new \Datetime();

    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdDate = new \Datetime();
        $this->modifiedDate = new \Datetime();
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
     * Set text
     *
     * @param string $text
     * @return Comment
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return Comment
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime 
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Set modifiedDate
     *
     * @param \DateTime $modifiedDate
     * @return Comment
     */
    public function setModifiedDate($modifiedDate)
    {
        $this->modifiedDate = $modifiedDate;

        return $this;
    }

    /**
     * Get modifiedDate
     *
     * @return \DateTime 
     */
    public function getModifiedDate()
    {
        return $this->modifiedDate;
    }

    /**
     * Set person
     *
     * @param \iahm\ContactBundle\Entity\Person $person
     * @return Comment
     */
    public function setPerson(\iahm\ContactBundle\Entity\Person $person = null)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person
     *
     * @return \iahm\ContactBundle\Entity\Person 
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set donation
     *
     * @param \iahm\ContactBundle\Entity\Donation $donation
     * @return Comment
     */
    public function setDonation(\iahm\ContactBundle\Entity\Donation $donation = null)
    {
        $this->donation = $donation;

        return $this;
    }

    /**
     * Get donation
     *
     * @return \iahm\ContactBundle\Entity\Donation 
     */
    public function getDonation()
    {
        return $this->donation;
    }

    /**
     * Set family
     *
     * @param \iahm\ContactBundle\Entity\Family $family
     * @return Comment
     */
    public function setFamily(\iahm\ContactBundle\Entity\Family $family = null)
    {
        $this->family = $family;

        return $this;
    }

    /**
     * Get family
     *
     * @return \iahm\ContactBundle\Entity\Family 
     */
    public function getFamily()
    {
        return $this->family;
    }
}
