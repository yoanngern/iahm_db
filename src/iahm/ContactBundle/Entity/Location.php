<?php

namespace iahm\ContactBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\AccessorOrder;

/**
 * Location
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="iahm\ContactBundle\Entity\LocationRepository")
 * @ORM\HasLifecycleCallbacks
 * @ExclusionPolicy("all")
 * @AccessorOrder("custom", custom = {"id", "type", "address", "postbox", "district", "city", "department",
 * "postalCode", "country", "latitude", "longitude", "entity", "createdDate", "modifiedDate"})
 *
 */
class Location
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255)
     * @Expose
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="postbox", type="string", length=255, nullable=true)
     * @Expose
     */
    private $postbox;

    /**
     * @var string
     *
     * @ORM\Column(name="district", type="string", length=255, nullable=true)
     * @Expose
     */
    private $district;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255)
     * @Expose
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="department", type="string", length=255, nullable=true)
     * @Expose
     */
    private $department;

    /**
     * @var string
     *
     * @ORM\Column(name="postalCode", type="string", length=255, nullable=true)
     * @Expose
     */
    private $postalCode;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255)
     * @Expose
     */
    private $country;

    /**
     * @var float
     *
     * @ORM\Column(name="latitude", type="float", nullable=true)
     * @Expose
     */
    private $latitude;

    /**
     * @var float
     *
     * @ORM\Column(name="longitude", type="float", nullable=true)
     * @Expose
     */
    private $longitude;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     * @Expose
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdDate", type="datetime")
     * @Expose
     * @Groups({"LocationDetails"})
     */
    private $createdDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modifiedDate", type="datetime")
     * @Expose
     * @Groups({"LocationDetails"})
     */
    private $modifiedDate;

    /**
     * @ORM\ManyToOne(targetEntity="iahm\ContactBundle\Entity\Family", inversedBy="locations")
     * @Expose
     * @Groups({"LocationDetails"})
     */
    private $entity;

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
     * Set address
     *
     * @param string $address
     * @return Location
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set postbox
     *
     * @param integer $postbox
     * @return Location
     */
    public function setPostbox($postbox)
    {
        $this->postbox = $postbox;

        return $this;
    }

    /**
     * Get postbox
     *
     * @return integer
     */
    public function getPostbox()
    {
        return $this->postbox;
    }

    /**
     * Set district
     *
     * @param string $district
     * @return Location
     */
    public function setDistrict($district)
    {
        $this->district = $district;

        return $this;
    }

    /**
     * Get district
     *
     * @return string
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Location
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set department
     *
     * @param string $department
     * @return Location
     */
    public function setDepartment($department)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return string
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set postalCode
     *
     * @param integer $postalCode
     * @return Location
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get postalCode
     *
     * @return integer
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return Location
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     * @return Location
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     * @return Location
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return Location
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
     * @return Location
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
     * Set type
     *
     * @param string $type
     * @return Location
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
     * Set entity
     *
     * @param \iahm\ContactBundle\Entity\Family $entity
     * @return Location
     */
    public function setEntity(\iahm\ContactBundle\Entity\Family $entity = null)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get entity
     *
     * @return \iahm\ContactBundle\Entity\Family
     */
    public function getEntity()
    {
        return $this->entity;
    }
}
