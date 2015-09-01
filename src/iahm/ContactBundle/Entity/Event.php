<?php

namespace iahm\ContactBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\AccessorOrder;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\Type;

/**
 * Event
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="iahm\ContactBundle\Entity\EventRepository")
 * @ORM\HasLifecycleCallbacks
 * @ExclusionPolicy("all")
 * @AccessorOrder("custom", custom = {"id", "title", "start", "end", "parent", "childrens", "persons", "units"})
 */
class Event
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
     * @ORM\Column(name="title", type="string", length=255)
     * @Expose
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start", type="datetime", nullable=true)
     * @Expose
     * @Groups({"EventDetails", "ContactEvents"})
     */
    private $start;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end", type="datetime", nullable=true)
     * @Expose
     * @Groups({"EventDetails", "ContactEvents"})
     */
    private $end;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdDate", type="datetime")
     * @Expose
     * @Groups({"EventDetails"})
     */
    private $createdDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modifiedDate", type="datetime")
     * @Expose
     * @Groups({"EventDetails"})
     */
    private $modifiedDate;

    /**
     * @ORM\ManyToMany(targetEntity="iahm\ContactBundle\Entity\Person", inversedBy="events", cascade={"merge", "detach"})
     * @Expose
     * @Groups({"EventDetails"})
     * @SerializedName("participants")
     */
    private $persons;

    /**
     * @ORM\OneToMany(targetEntity="iahm\ContactBundle\Entity\Unit", mappedBy="event", cascade={"persist", "remove"})
     * @Expose
     * @Groups({"EventDetails", "EventGroups"})
     */
    private $groups;

    /**
     * @ORM\OneToMany(targetEntity="iahm\ContactBundle\Entity\Event", mappedBy="parent", cascade={"persist", "remove", "merge"})
     * @Expose
     * @Groups({"EventDetails"})
     * @Accessor(getter="getSimpleChildrens",setter="setChildrens")
     * @Type("array")
     */
    private $childrens;

    /**
     * @ORM\ManyToOne(targetEntity="iahm\ContactBundle\Entity\Event", inversedBy="childrens", cascade={"merge", "detach"})
     * @Expose
     * @Groups({"EventDetails"})
     * @Accessor(getter="getSimpleParent",setter="setParent")
     * @Type("array")
     */
    private $parent;

    /**
     * @return array
     */
    public function getSimpleParent()
    {
        if ($this->getParent()) {
            $parent["id"] = trim($this->getParent()->getId());
            $parent["title"] = trim($this->getParent()->getTitle());
        } else {
            $parent = null;
        }
        return $parent;
    }


    /**
     * @return array
     */
    public function getSimpleChildrens()
    {
        $childrens = Array();

        foreach ($this->getChildrens() as $event) {
            $children["id"] = trim($event->getId());
            $children["title"] = trim($event->getTitle());
            $children["start"] = $event->getStart();
            $children["end"] = $event->getEnd();

            $childrens[] = $children;
        }

        return $childrens;
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
     * Add groups
     *
     * @param \iahm\ContactBundle\Entity\Unit $groups
     * @return Event
     */
    public function addGroup(\iahm\ContactBundle\Entity\Unit $groups)
    {
        $this->groups[] = $groups;
        $groups->setEvent($this);

        return $this;
    }

    /**
     * @param $doc
     * @return mixed
     */
    public function toSolrDocument($doc)
    {
        $doc->doc_id = "event_" . $this->getId();
        $doc->doc_type = "event";
        $doc->doc_title = $this->getTitle();
        $doc->doc_description = $this->getStart()->format('d.m.Y') . " - " . $this->getEnd()->format('d.m.Y');

        $doc->entity_id = $this->getId();
        $doc->createdDate = $this->getCreatedDate();
        $doc->modifiedDate = $this->getModifiedDate();

        $doc->title = $this->getTitle();

        $doc->start = $this->getStart();
        $doc->end = $this->getEnd();

        if ($this->getParent() != null) {
            $doc->event_parent = $this->getParent()->getTitle();
        }

        if (sizeof($this->getPersons())) {
            $participants = [];
            foreach ($this->getPersons() as $contact) {
                $participants[] = $contact->getFirstname() . " " . $contact->getLastname();
            }
            $doc->participants = $participants;
        }

        if (sizeof($this->getGroups())) {
            $event_groups = [];
            foreach ($this->getGroups() as $group) {
                $event_groups[] = $group->getTitle();
            }
            $doc->event_groups = $event_groups;
        }

        if (sizeof($this->getChildrens())) {
            $event_childrens = [];
            foreach ($this->getChildrens() as $event) {
                $event_childrens[] = $event->getTitle();
            }
            $doc->event_childrens = $event_childrens;
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
        $this->persons = new \Doctrine\Common\Collections\ArrayCollection();
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set title
     *
     * @param string $title
     * @return Event
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
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return Event
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
     * @return Event
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
     * Add persons
     *
     * @param \iahm\ContactBundle\Entity\Person $persons
     * @return Event
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


    /**
     * Set start
     *
     * @param \DateTime $start
     * @return Event
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set end
     *
     * @param \DateTime $end
     * @return Event
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Add childrens
     *
     * @param \iahm\ContactBundle\Entity\Event $childrens
     * @return Event
     */
    public function addChildren(\iahm\ContactBundle\Entity\Event $childrens)
    {
        $this->childrens[] = $childrens;
        $childrens->setParent($this);

        return $this;
    }

    /**
     * Remove childrens
     *
     * @param \iahm\ContactBundle\Entity\Event $childrens
     */
    public function removeChildren(\iahm\ContactBundle\Entity\Event $childrens)
    {
        $this->childrens->removeElement($childrens);
        $childrens->setParent(null);
    }

    /**
     * Get childrens
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildrens()
    {
        return $this->childrens;
    }

    /**
     * Set parent
     *
     * @param \iahm\ContactBundle\Entity\Event $parent
     * @return Event
     */
    public function setParent(\iahm\ContactBundle\Entity\Event $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \iahm\ContactBundle\Entity\Event
     */
    public function getParent()
    {
        return $this->parent;
    }


    /**
     * Remove groups
     *
     * @param \iahm\ContactBundle\Entity\Unit $groups
     */
    public function removeGroup(\iahm\ContactBundle\Entity\Unit $groups)
    {
        $this->groups->removeElement($groups);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroups()
    {
        return $this->groups;
    }
}
