<?php

namespace iahm\ContactBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\AccessorOrder;
use JMS\Serializer\Annotation\SerializedName;

/**
 * Person
 *
 * @ORM\Table(name="person")
 * @ORM\Entity(repositoryClass="iahm\ContactBundle\Entity\PersonRepository")
 * @ORM\HasLifecycleCallbacks
 * @ExclusionPolicy("all")
 * @AccessorOrder("custom", custom = {"id", "firstname", "lastname", "title", "gender", "dateOfBirth", "phones",
 * "emails", "languages", "entities", "comment", "tasks"})
 */
class Person
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
     * @ORM\Column(name="firstname", type="string", length=255)
     * @Expose
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255)
     * @Expose
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Expose
     * @Groups({"ContactDetails"})
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=255)
     * @Expose
     * @Groups({"ContactDetails"})
     */
    private $gender;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateOfBirth", type="date")
     * @Expose
     * @Groups({"ContactDetails"})
     */
    private $dateOfBirth;

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
     * @ORM\OneToMany(targetEntity="iahm\ContactBundle\Entity\Phone", mappedBy="person", cascade={"persist", "remove"})
     * @Expose
     * @Groups({"ContactDetails"})
     */
    private $phones;

    /**
     * @ORM\OneToMany(targetEntity="iahm\ContactBundle\Entity\Email", mappedBy="person", cascade={"persist", "remove"})
     * @Expose
     * @Groups({"ContactDetails"})
     */
    private $emails;

    /**
     * @ORM\ManyToMany(targetEntity="iahm\ContactBundle\Entity\Language", inversedBy="persons")
     * @Expose
     * @Groups({"ContactDetails"})
     */
    private $languages;

    /**
     * @ORM\ManyToMany(targetEntity="iahm\ContactBundle\Entity\Event", mappedBy="persons", cascade={"persist", "merge", "detach"})
     * @Expose
     * @Groups({"ContactEvents"})
     */
    private $events;


    /**
     * @ORM\OneToMany(targetEntity="iahm\ContactBundle\Entity\PersonType", mappedBy="person")
     * @Expose
     * @Groups({"ContactDetails"})
     * @SerializedName("entities")
     */
    private $personsTypes;

    /**
     * @ORM\OneToOne(targetEntity="iahm\ContactBundle\Entity\Comment", mappedBy="person", cascade={"persist", "remove"})
     * @Expose
     * @Accessor(getter="getCustomComment",setter="setComment")
     * @Groups({"ContactDetails"})
     * @Type("string")
     */
    private $comment;

    /**
     * @ORM\ManyToMany(targetEntity="iahm\ContactBundle\Entity\Unit", mappedBy="members", cascade={"persist", "merge", "detach"})
     * @SerializedName("memberOfGroups")
     * @Expose
     * @Groups({"ContactGroups", "ContactMembers"})
     */
    private $memberOfs;

    /**
     * @ORM\ManyToMany(targetEntity="iahm\ContactBundle\Entity\Unit", mappedBy="leaders", cascade={"persist", "merge", "detach"})
     * @SerializedName("leaderOfGroups")
     * @Expose
     * @Groups({"ContactGroups", "ContactLeaders"})
     */
    private $leaderOfs;

    /**
     * @ORM\OneToMany(targetEntity="iahm\ContactBundle\Entity\Donation", mappedBy="person", cascade={"persist", "merge", "detach"})
     * @Expose
     * @Groups({"ContactDonations"})
     */
    private $donations;

    /**
     * @var string
     */
    private $type;

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
            $comment_txt = "The donation's author has been deleted. His name was: " . $this->firstname . " " . $this->lastname . "\r\n - - - - \r\n \r\n" . $comment_txt;
            if ($donation->getComment() != null) {
                $donation->getComment()->setText($comment_txt);
            } else {
                $comment = new Comment();
                $comment->setText($comment_txt);
                $donation->setComment($comment);
            }
            $donation->setPerson(null);
        }

    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }


    /**
     * @param $doc
     * @return mixed
     */
    public function toSolrDocument($doc)
    {
        $doc->doc_id = "contact_" . $this->getId();
        $doc->doc_type = "contact";
        $doc->doc_title = $this->getFirstname() . " " . $this->getLastname();

        $desc = "";

        if(sizeof($this->getEmails())) {
            $desc .= $this->getEmails()[0]->getValue();
        }

        if(sizeof($this->getPersonsTypes())) {
            if(sizeof($this->getPersonsTypes()[0]->getFamily()->getLocations())) {

                if($desc != "") {
                    $desc .= "\r\n";
                }

                $desc .= $this->getPersonsTypes()[0]->getFamily()->getLocations()[0]->getCity();
                $desc .= " (" .$this->getPersonsTypes()[0]->getFamily()->getLocations()[0]->getCountry() . ")";
            }
        }

        $doc->doc_description = $desc;
        //$doc->doc_description = "..."; //$this->getPersonsTypes()[0]->getFamily()->getLocations()[0]->getCity() . "(" - $this->getPersonsTypes()[0]->getFamily()->getLocations()[0]->getCountry() . ")";

        $doc->entity_id = $this->getId();
        $doc->createdDate = $this->getCreatedDate();
        $doc->modifiedDate = $this->getModifiedDate();
        $doc->firstname = $this->getFirstname();
        $doc->lastname = $this->getLastname();
        $doc->contact_title = $this->getTitle();
        $doc->gender = $this->getGender();
        $doc->dateOfBirth = $this->getDateOfBirth();

        if ($this->getComment() != null) {
            $doc->comment = $this->getComment()->getText();
        }

        $emails = [];
        foreach ($this->getEmails() as $email) {
            $emails[] = $email->getValue();
        }

        $doc->emails = $emails;

        return $doc;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdDate = new \Datetime();
        $this->modifiedDate = new \Datetime();
        $this->phones = new \Doctrine\Common\Collections\ArrayCollection();
        $this->emails = new \Doctrine\Common\Collections\ArrayCollection();
        $this->languages = new \Doctrine\Common\Collections\ArrayCollection();
        $this->events = new \Doctrine\Common\Collections\ArrayCollection();
        $this->personsTypes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->donations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->memberOfs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->leaderOfs = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set firstname
     *
     * @param string $firstname
     * @return Person
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return Person
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Person
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return Person
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set dateOfBirth
     *
     * @param \DateTime $dateOfBirth
     * @return Person
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    /**
     * Get dateOfBirth
     *
     * @return \DateTime
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return Person
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
     * @return Person
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
     * Add phones
     *
     * @param \iahm\ContactBundle\Entity\Phone $phones
     * @return Person
     */
    public function addPhone(\iahm\ContactBundle\Entity\Phone $phones)
    {
        $this->phones[] = $phones;
        $phones->setPerson($this);

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
        $phones->setPerson(null);
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
     * @return Person
     */
    public function addEmail(\iahm\ContactBundle\Entity\Email $emails)
    {
        $this->emails[] = $emails;
        $emails->setPerson($this);

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
        $emails->setPerson(null);
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

    /**
     * Add languages
     *
     * @param \iahm\ContactBundle\Entity\Language $languages
     * @return Person
     */
    public function addLanguage(\iahm\ContactBundle\Entity\Language $languages)
    {
        $this->languages[] = $languages;

        return $this;
    }

    /**
     * Remove languages
     *
     * @param \iahm\ContactBundle\Entity\Language $languages
     */
    public function removeLanguage(\iahm\ContactBundle\Entity\Language $languages)
    {
        $this->languages->removeElement($languages);
    }

    /**
     * Get languages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * Add events
     *
     * @param \iahm\ContactBundle\Entity\Event $events
     * @return Person
     */
    public function addEvent(\iahm\ContactBundle\Entity\Event $events)
    {
        $this->events[] = $events;
        $events->addPerson($this);

        return $this;
    }

    /**
     * Remove events
     *
     * @param \iahm\ContactBundle\Entity\Event $events
     */
    public function removeEvent(\iahm\ContactBundle\Entity\Event $events)
    {
        $this->events->removeElement($events);
        $events->removePerson($this);
    }

    /**
     * Get events
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Add personsTypes
     *
     * @param \iahm\ContactBundle\Entity\PersonType $personsTypes
     * @return Person
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
     * Set comment
     *
     * @param \iahm\ContactBundle\Entity\Comment $comment
     * @return Person
     */
    public function setComment(\iahm\ContactBundle\Entity\Comment $comment = null)
    {
        $this->comment = $comment;
        $comment->setPerson($this);

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
     * Add donations
     *
     * @param \iahm\ContactBundle\Entity\Donation $donations
     * @return Person
     */
    public function addDonation(\iahm\ContactBundle\Entity\Donation $donations)
    {
        $this->donations[] = $donations;
        $donations->setPerson($this);

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
        $donations->setPerson(null);
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
     * Add memberOfs
     *
     * @param \iahm\ContactBundle\Entity\Unit $memberOfs
     * @return Person
     */
    public function addMemberOf(\iahm\ContactBundle\Entity\Unit $memberOfs)
    {
        $this->memberOfs[] = $memberOfs;
        $memberOfs->addMember($this);

        return $this;
    }

    /**
     * Remove memberOfs
     *
     * @param \iahm\ContactBundle\Entity\Unit $memberOfs
     */
    public function removeMemberOf(\iahm\ContactBundle\Entity\Unit $memberOfs)
    {
        $this->memberOfs->removeElement($memberOfs);
        $memberOfs->removeMember($this);
    }

    /**
     * Get memberOfs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMemberOfs()
    {
        return $this->memberOfs;
    }

    /**
     * Add leaderOfs
     *
     * @param \iahm\ContactBundle\Entity\Unit $leaderOfs
     * @return Person
     */
    public function addLeaderOf(\iahm\ContactBundle\Entity\Unit $leaderOfs)
    {
        $this->leaderOfs[] = $leaderOfs;
        $leaderOfs->addLeader($this);

        return $this;
    }

    /**
     * Remove leaderOfs
     *
     * @param \iahm\ContactBundle\Entity\Unit $leaderOfs
     */
    public function removeLeaderOf(\iahm\ContactBundle\Entity\Unit $leaderOfs)
    {
        $this->leaderOfs->removeElement($leaderOfs);
        $leaderOfs->removeLeader($this);
    }

    /**
     * Get leaderOfs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLeaderOfs()
    {
        return $this->leaderOfs;
    }
}
