<?php

namespace iahm\ContactBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\AccessorOrder;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;

/**
 * Family
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="iahm\ContactBundle\Entity\FamilyRepository")
 * @ORM\HasLifecycleCallbacks
 * @ExclusionPolicy("all")
 * @AccessorOrder("custom", custom = {"id", "name", "type", "persons", "locations", "units", "donations", "comment"})
 */
class Family
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
     * @ORM\Column(name="type", type="string", length=255)
     * @Expose
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     * @Expose
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdDate", type="datetime")
     * @Expose
     * @Groups({"EntityDetails"})
     */
    private $createdDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modifiedDate", type="datetime")
     * @Expose
     * @Groups({"EntityDetails"})
     */
    private $modifiedDate;

    /**
     * @ORM\OneToMany(targetEntity="iahm\ContactBundle\Entity\PersonType", mappedBy="family", cascade={"persist", "remove"})
     * @SerializedName("contacts")
     * @Expose
     * @Groups({"EntityDetails"})
     */
    private $personsTypes;

    /**
     * @ORM\OneToMany(targetEntity="iahm\ContactBundle\Entity\Phone", mappedBy="entity", cascade={"persist", "remove"})
     * @Expose
     * @Groups({"EntityDetails"})
     */
    private $phones;

    /**
     * @ORM\OneToMany(targetEntity="iahm\ContactBundle\Entity\Email", mappedBy="entity", cascade={"persist", "remove"})
     * @Expose
     * @Groups({"EntityDetails"})
     */
    private $emails;

    /**
     * @ORM\OneToMany(targetEntity="iahm\ContactBundle\Entity\Location", mappedBy="entity", cascade={"persist", "remove"})
     * @Expose
     * @Groups({"EntityDetails", "ContactDetails"})
     */
    private $locations;

    /**
     * @ORM\OneToMany(targetEntity="iahm\ContactBundle\Entity\Donation", mappedBy="entity", cascade={"detach", "merge"})
     * @Expose
     * @Groups({"EntityDonations"})
     */
    private $donations;

    /**
     * @ORM\ManyToMany(targetEntity="iahm\ContactBundle\Entity\Unit", mappedBy="entities")
     * @Expose
     * @Groups({"EntityGroups"})
     * @SerializedName("groups")
     */
    private $units;

    /**
     * @ORM\OneToOne(targetEntity="iahm\ContactBundle\Entity\Comment", mappedBy="family", cascade={"persist", "remove"})
     * @Expose
     * @Accessor(getter="getCustomComment",setter="setComment")
     * @Groups({"EntityDetails"})
     * @Type("string")
     */
    private $comment;

    /**
     * @var string
     */
    private $comment_txt;


    /**
     * Set CommentText
     *
     * @param string
     * @return string
     */
    public function setCommentTxt($txt)
    {
        $this->comment_txt = $txt;

        return $this;
    }

    /**
     * Get CommentText
     *
     * @return string
     */
    public function getCommentTxt()
    {

        return $this->comment_txt;

    }

    /**
     * Get the formatted comment
     *
     * @return String
     */
    public function getCustomComment()
    {
        if ($this->getComment() != null) {
            return $this->getComment()->getText();
        } else {
            return "";
        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preSave()
    {
        $this->modifiedDate = new \Datetime();

    }

    /**
     * @ORM\PreRemove()
     */
    public function preDelete()
    {
        foreach ($this->getDonations() as $donation) {
            if ($donation->getComment() != null) {
                $comment_txt = $donation->getComment()->getText();
            } else {
                $comment_txt = "";
            }
            $comment_txt = "The donation's author has been deleted. His name was: " . $this->name . "\r\n - - - - \r\n \r\n" . $comment_txt;
            if ($donation->getComment() != null) {
                $donation->getComment()->setText($comment_txt);
            } else {
                $comment = new Comment();
                $comment->setText($comment_txt);
                $donation->setComment($comment);
            }
            $donation->setEntity(null);
        }

    }

    /**
     * @param $doc
     * @return mixed
     */
    public function toSolrDocument($doc)
    {
        $doc->doc_id = "entity_" . $this->getId();
        $doc->doc_type = "entity";
        $doc->doc_title = $this->getName();
        $doc->doc_description = "...";

        $doc->entity_id = $this->getId();
        $doc->createdDate = $this->getCreatedDate();
        $doc->modifiedDate = $this->getModifiedDate();

        if ($this->getComment() != null) {
            $doc->comment = $this->getComment()->getText();
        }

        $doc->type = $this->getType();

        $doc->title = $this->getName();

        $members = [];
        foreach ($this->getPersonsTypes() as $personType) {
            $members[] = $personType->getPerson()->getFirstname() . " " . $personType->getPerson()->getLastname();
        }

        $location_address = [];
        $location_city = [];
        $location_department = [];
        $location_postalCode = [];
        $location_country = [];
        foreach ($this->getLocations() as $location) {
            $location_address[] = $location->getAddress() ." ". $location->getPostBox() ." ". $location->getDistrict();
            $location_city[] = $location->getCity();
            $location_department[] = $location->getDepartment();
            $location_postalCode[] = $location->getPostalCode();
            $location_country[] = $location->getCountry();
        }

        $doc->members = $members;
        $doc->location_address = $location_address;
        $doc->location_city = $location_city;
        $doc->location_department = $location_department;
        $doc->location_postalCode = $location_postalCode;
        $doc->location_country = $location_country;


        return $doc;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdDate = new \Datetime();
        $this->modifiedDate = new \Datetime();
        $this->personsTypes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->locations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->donations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->units = new \Doctrine\Common\Collections\ArrayCollection();
        $this->phones = new \Doctrine\Common\Collections\ArrayCollection();
        $this->emails = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set type
     *
     * @param string $type
     * @return Family
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
     * Set name
     *
     * @param string $name
     * @return Family
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return Family
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
     * @return Family
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
     * Add personsTypes
     *
     * @param \iahm\ContactBundle\Entity\PersonType $personsTypes
     * @return Family
     */
    public function addPersonsType(\iahm\ContactBundle\Entity\PersonType $personsTypes)
    {
        $this->personsTypes[] = $personsTypes;

        return $this;
    }

    /**
     * Remove personsTypes
     *
     * @param \iahm\ContactBundle\Entity\PersonType $personsTypes
     */
    public function removePersonsType(\iahm\ContactBundle\Entity\PersonType $personsTypes)
    {
        $this->personsTypes->removeElement($personsTypes);
    }

    /**
     * Get personsTypes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPersonsTypes()
    {
        return $this->personsTypes;
    }

    /**
     * Add locations
     *
     * @param \iahm\ContactBundle\Entity\Location $locations
     * @return Family
     */
    public function addLocation(\iahm\ContactBundle\Entity\Location $locations)
    {
        $this->locations[] = $locations;
        $locations->setEntity($this);

        return $this;
    }

    /**
     * Remove locations
     *
     * @param \iahm\ContactBundle\Entity\Location $locations
     */
    public function removeLocation(\iahm\ContactBundle\Entity\Location $locations)
    {
        $this->locations->removeElement($locations);
    }

    /**
     * Get locations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     * Add donations
     *
     * @param \iahm\ContactBundle\Entity\Donation $donations
     * @return Family
     */
    public function addDonation(\iahm\ContactBundle\Entity\Donation $donations)
    {
        $this->donations[] = $donations;
        $donations->setEntity($this);

        return $this;
    }

    /**
     * Remove donations
     *
     * @param \iahm\ContactBundle\Entity\Donation $donations
     */
    public function removeDonation(\iahm\ContactBundle\Entity\Donation $donations)
    {
        $this->donations->removeElement($donations);
    }

    /**
     * Get donations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDonations()
    {
        return $this->donations;
    }

    /**
     * Add units
     *
     * @param \iahm\ContactBundle\Entity\Unit $units
     * @return Family
     */
    public function addUnit(\iahm\ContactBundle\Entity\Unit $units)
    {
        $this->units[] = $units;

        return $this;
    }

    /**
     * Remove units
     *
     * @param \iahm\ContactBundle\Entity\Unit $units
     */
    public function removeUnit(\iahm\ContactBundle\Entity\Unit $units)
    {
        $this->units->removeElement($units);
    }

    /**
     * Get units
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUnits()
    {
        return $this->units;
    }

    /**
     * Set comment
     *
     * @param \iahm\ContactBundle\Entity\Comment $comment
     * @return Family
     */
    public function setComment(\iahm\ContactBundle\Entity\Comment $comment = null)
    {
        $this->comment = $comment;
        $comment->setFamily($this);

        return $this;
    }

    /**
     * Get comment
     *
     * @return \iahm\ContactBundle\Entity\Comment
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Add phones
     *
     * @param \iahm\ContactBundle\Entity\Phone $phones
     * @return Family
     */
    public function addPhone(\iahm\ContactBundle\Entity\Phone $phones)
    {
        $this->phones[] = $phones;
        $phones->setEntity($this);

        return $this;
    }

    /**
     * Remove phones
     *
     * @param \iahm\ContactBundle\Entity\Phone $phones
     */
    public function removePhone(\iahm\ContactBundle\Entity\Phone $phones)
    {
        $this->phones->removeElement($phones);
        $phones->setEntity(null);
    }

    /**
     * Get phones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * Add emails
     *
     * @param \iahm\ContactBundle\Entity\Email $emails
     * @return Family
     */
    public function addEmail(\iahm\ContactBundle\Entity\Email $emails)
    {
        $this->emails[] = $emails;
        $emails->setEntity($this);

        return $this;
    }

    /**
     * Remove emails
     *
     * @param \iahm\ContactBundle\Entity\Email $emails
     */
    public function removeEmail(\iahm\ContactBundle\Entity\Email $emails)
    {
        $this->emails->removeElement($emails);
        $emails->setEntity(null);
    }

    /**
     * Get emails
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEmails()
    {
        return $this->emails;
    }
}
