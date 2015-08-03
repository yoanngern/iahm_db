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
 * Donation
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="iahm\ContactBundle\Entity\DonationRepository")
 * @ORM\HasLifecycleCallbacks
 * @ExclusionPolicy("all")
 */
class Donation
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     * @Expose
     */
    private $date;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     * @Expose
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=255)
     * @Expose
     */
    private $currency;

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
     * @Groups({"DonationDetails"})
     */
    private $createdDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modifiedDate", type="datetime")
     * @Expose
     * @Groups({"DonationDetails"})
     */
    private $modifiedDate;

    /**
     * @ORM\ManyToOne(targetEntity="iahm\ContactBundle\Entity\Person", inversedBy="donations", cascade={"detach", "merge"})
     * @Expose
     * @Groups({"DonationDetails"})
     * @SerializedName("contact")
     */
    private $person;

    /**
     * @ORM\OneToOne(targetEntity="iahm\ContactBundle\Entity\Comment", mappedBy="donation", cascade={"persist", "remove"})
     * @Expose
     * @Accessor(getter="getCustomComment",setter="setComment")
     * @Groups({"DonationDetails", "ContactDonations", "EntityDonations"})
     * @Type("string")
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity="iahm\ContactBundle\Entity\Family", inversedBy="donations", cascade={"detach", "merge"})
     * @Expose
     * @Groups({"DonationDetails"})
     * @SerializedName("entity")
     */
    private $entity;

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
     * @param $doc
     * @return mixed
     */
    public function toSolrDocument($doc)
    {
        $doc->doc_id = "donation_" . $this->getId();
        $doc->doc_type = "donation";

        if($this->getPerson() != null) {
            $doc->doc_title = $this->getPerson()->getFirstname() ." ". $this->getPerson()->getFirstname() ." : ". $this->getAmount() . " " . $this->getCurrency();
        } else {
            $doc->doc_title = $this->getAmount() ." ". $this->getCurrency();
        }

        $doc->doc_description = $this->getDate()->format('d.m.Y');

        $doc->entity_id = $this->getId();
        $doc->createdDate = $this->getCreatedDate();
        $doc->modifiedDate = $this->getModifiedDate();

        $doc->type = $this->getType();

        $doc->date = $this->getDate();
        $doc->amount = $this->getAmount();
        $doc->currency = $this->getCurrency();

        if ($this->getComment() != null) {
            $doc->comment = $this->getComment()->getText();
        }

        return $doc;
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
     * Set date
     *
     * @param \DateTime $date
     * @return Donation
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
     * Set amount
     *
     * @param float $amount
     * @return Donation
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set currency
     *
     * @param string $currency
     * @return Donation
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Donation
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
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return Donation
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
     * @return Donation
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
     * @return Donation
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
     * Get comment
     *
     * @return \iahm\ContactBundle\Entity\Comment
     */
    public function getComment()
    {
        return $this->comment;
    }


    /**
     * Set entity
     *
     * @param \iahm\ContactBundle\Entity\Family $entity
     * @return Donation
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

    /**
     * Set comment
     *
     * @param \iahm\ContactBundle\Entity\Comment $comment
     * @return Donation
     */
    public function setComment(\iahm\ContactBundle\Entity\Comment $comment = null)
    {
        $this->comment = $comment;
        $comment->setDonation($this);

        return $this;
    }
}
