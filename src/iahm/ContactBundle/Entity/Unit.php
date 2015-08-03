<?php

namespace iahm\ContactBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\MaxDepth;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\AccessorOrder;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Unit
 *
 * @ORM\Table(name="unit")
 * @ORM\Entity(repositoryClass="iahm\ContactBundle\Entity\UnitRepository")
 * @ORM\HasLifecycleCallbacks
 * @ExclusionPolicy("all")
 * @AccessorOrder("custom", custom = {"id", "title", "parent", "childrens", "leaders", "members", "entities", "event"})
 */
class Unit
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
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdDate", type="datetime")
     * @Expose
     * @Groups({"GroupDetails"})
     */
    private $createdDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modifiedDate", type="datetime")
     * @Expose
     * @Groups({"GroupDetails"})
     */
    private $modifiedDate;

    /**
     * @ORM\ManyToMany(targetEntity="iahm\ContactBundle\Entity\Person", inversedBy="memberOfs", cascade={"merge", "detach"})
     * @ORM\JoinTable(name="unit_member")
     * @Expose
     * @Groups({"GroupDetails"})
     */
    private $members;

    /**
     * @ORM\ManyToMany(targetEntity="iahm\ContactBundle\Entity\Person", inversedBy="leaderOfs", cascade={"merge", "detach"})
     * @ORM\JoinTable(name="unit_leader")
     * @Expose
     * @Groups({"GroupDetails"})
     */
    private $leaders;

    /**
     * @ORM\ManyToMany(targetEntity="iahm\ContactBundle\Entity\Family", inversedBy="units")
     * @Expose
     * @Groups({"GroupDetails"})
     */
    private $entities;

    /**
     * @ORM\ManyToOne(targetEntity="iahm\ContactBundle\Entity\Event", inversedBy="groups", cascade={"merge"})
     * @Expose
     * @Groups({"GroupDetails"})
     */
    private $event;

    /**
     * @ORM\OneToMany(targetEntity="iahm\ContactBundle\Entity\Unit", mappedBy="parent", cascade={"persist", "remove", "merge"})
     * @Expose
     * @Groups({"GroupDetails"})
     * @Accessor(getter="getSimpleChildrens",setter="setChildrens")
     * @Type("array")
     */
    private $childrens;

    /**
     * @ORM\ManyToOne(targetEntity="iahm\ContactBundle\Entity\Unit", inversedBy="childrens", cascade={"merge", "detach"})
     * @Expose
     * @Groups({"GroupDetails"})
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

        foreach ($this->getChildrens() as $group) {
            $children["id"] = trim($group->getId());
            $children["title"] = trim($group->getTitle());

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
     * @param $doc
     * @return mixed
     */
    public function toSolrDocument($doc)
    {
        $doc->doc_id = "group_" . $this->getId();
        $doc->doc_type = "group";
        $doc->doc_title = $this->getTitle();
        $doc->doc_description = "...";

        $doc->entity_id = $this->getId();
        $doc->createdDate = $this->getCreatedDate();
        $doc->modifiedDate = $this->getModifiedDate();

        $doc->title = $this->getTitle();

        if($this->getParent() != null) {
            $doc->group_parent = $this->getParent()->getTitle();
        }

        $group_childrens = [];
        foreach ($this->getChildrens() as $group) {
            $group_childrens[] = $group->getTitle();
        }

        $group_members = [];
        foreach ($this->getMembers() as $contact) {
            $group_members[] = $contact->getFirstname() ." ". $contact->getLastname();
        }

        $group_leaders = [];
        foreach ($this->getLeaders() as $contact) {
            $group_leaders[] = $contact->getFirstname() ." ". $contact->getLastname();
        }

        $group_entities = [];
        foreach ($this->getEntities() as $entity) {
            $group_entities[] = $entity->getName();
        }

        $doc->group_childrens = $group_childrens;
        $doc->group_members = $group_members;
        $doc->group_leaders = $group_leaders;
        $doc->group_entities = $group_entities;

        return $doc;
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdDate = new \Datetime();
        $this->modifiedDate = new \Datetime();
        $this->members = new \Doctrine\Common\Collections\ArrayCollection();
        $this->leaders = new \Doctrine\Common\Collections\ArrayCollection();
        $this->entities = new \Doctrine\Common\Collections\ArrayCollection();
        $this->childrens = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Unit
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
     * @return Unit
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
     * @return Unit
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
     * Set event
     *
     * @param \iahm\ContactBundle\Entity\Event $event
     * @return Unit
     */
    public function setEvent(\iahm\ContactBundle\Entity\Event $event = null)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return \iahm\ContactBundle\Entity\Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Add members
     *
     * @param \iahm\ContactBundle\Entity\Person $members
     * @return Unit
     */
    public function addMember(\iahm\ContactBundle\Entity\Person $members)
    {
        $this->members[] = $members;

        return $this;
    }

    /**
     * Remove members
     *
     * @param \iahm\ContactBundle\Entity\Person $members
     */
    public function removeMember(\iahm\ContactBundle\Entity\Person $members)
    {
        $this->members->removeElement($members);
    }

    /**
     * Get members
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * Add leaders
     *
     * @param \iahm\ContactBundle\Entity\Person $leaders
     * @return Unit
     */
    public function addLeader(\iahm\ContactBundle\Entity\Person $leaders)
    {
        $this->leaders[] = $leaders;

        return $this;
    }

    /**
     * Remove leaders
     *
     * @param \iahm\ContactBundle\Entity\Person $leaders
     */
    public function removeLeader(\iahm\ContactBundle\Entity\Person $leaders)
    {
        $this->leaders->removeElement($leaders);
    }

    /**
     * Get leaders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLeaders()
    {
        return $this->leaders;
    }

    /**
     * Add childrens
     *
     * @param \iahm\ContactBundle\Entity\Unit $childrens
     * @return Unit
     */
    public function addChildren(\iahm\ContactBundle\Entity\Unit $childrens)
    {
        $this->childrens[] = $childrens;
        $childrens->setParent($this);

        return $this;
    }

    /**
     * Remove childrens
     *
     * @param \iahm\ContactBundle\Entity\Unit $childrens
     */
    public function removeChildren(\iahm\ContactBundle\Entity\Unit $childrens)
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
     * @param \iahm\ContactBundle\Entity\Unit $parent
     * @return Unit
     */
    public function setParent(\iahm\ContactBundle\Entity\Unit $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \iahm\ContactBundle\Entity\Unit
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add entities
     *
     * @param \iahm\ContactBundle\Entity\Family $entities
     * @return Unit
     */
    public function addEntity(\iahm\ContactBundle\Entity\Family $entities)
    {
        $this->entities[] = $entities;

        return $this;
    }

    /**
     * Remove entities
     *
     * @param \iahm\ContactBundle\Entity\Family $entities
     */
    public function removeEntity(\iahm\ContactBundle\Entity\Family $entities)
    {
        $this->entities->removeElement($entities);
    }

    /**
     * Get entities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEntities()
    {
        return $this->entities;
    }
}
