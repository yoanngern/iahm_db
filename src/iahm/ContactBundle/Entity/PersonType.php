<?php

namespace iahm\ContactBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\SerializedName;

/**
 * PersonType
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="iahm\ContactBundle\Entity\PersonTypeRepository")
 * @ORM\HasLifecycleCallbacks
 * @ExclusionPolicy("all")
 */
class PersonType
{

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     * @Expose
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="iahm\ContactBundle\Entity\Person", inversedBy="personsTypes")
     * @ORM\Id
     * @SerializedName("contact")
     * @Expose
     */
    private $person;

    /**
     * @ORM\ManyToOne(targetEntity="iahm\ContactBundle\Entity\Family", inversedBy="personsTypes")
     * @ORM\Id
     * @SerializedName("entity")
     * @Expose
     */
    private $family;

    /**
     * Constructor
     */
    public function __construct(Person $person, Family $entity)
    {
        $this->person = $person;
        $this->family = $entity;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return PersonType
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
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
     * Get family
     *
     * @return \iahm\ContactBundle\Entity\Family 
     */
    public function getFamily()
    {
        return $this->family;
    }
}
