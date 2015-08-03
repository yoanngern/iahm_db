<?php

namespace iahm\ContactBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;

/**
 * Language
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="iahm\ContactBundle\Entity\LanguageRepository")
 * @ORM\HasLifecycleCallbacks
 * @ExclusionPolicy("all")
 */
class Language
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=255)
     * @ORM\Id
     * @Expose
     */
    private $ref;

    /**
     * @ORM\ManyToMany(targetEntity="iahm\ContactBundle\Entity\Person", mappedBy="languages")
     */
    private $persons;

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
        $this->persons = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Display a Person
     * @return String
     */
    public function __toString()
    {

        $ref = $this->ref;

        return $ref;
    }


    /**
     * Set ref
     *
     * @param string $ref
     * @return Language
     */
    public function setRef($ref)
    {
        $this->ref = $ref;

        return $this;
    }

    /**
     * Get ref
     *
     * @return string
     */
    public function getRef()
    {
        return $this->ref;
    }


    /**
     * Add persons
     *
     * @param \iahm\ContactBundle\Entity\Person $persons
     * @return Language
     */
    public function addPerson(\iahm\ContactBundle\Entity\Person $persons)
    {
        $this->persons[] = $persons;

        return $this;
    }

    /**
     * Remove persons
     *
     * @param \iahm\ContactBundle\Entity\Person $persons
     */
    public function removePerson(\iahm\ContactBundle\Entity\Person $persons)
    {
        $this->persons->removeElement($persons);
    }

    /**
     * Get persons
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPersons()
    {
        return $this->persons;
    }
}
