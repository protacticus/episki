<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Finding
 *
 * @ORM\Table(name="finding")
 * @ORM\Entity(repositoryClass="App\Repository\FindingRepository")
 */
class Finding
{
	// Number of controls to display by default
	// const NUM_ITEMS = 10;
	
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=50)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=1000, nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Controls")
     * @ORM\JoinColumn(name="controlref", referencedColumnName="id")
     */
    private $controlref;
    
     /**
     * @var User[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\User", cascade={"persist"})
     * @ORM\JoinTable(name="finding_owners")
     * @ORM\OrderBy({"fullName": "ASC"})
     * @Assert\Count(max="5", maxMessage="controls.too_many_owners")
     */
    private $owners;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Finding
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
     * Set description
     *
     * @param string $description
     *
     * @return Finding
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set controlref
     *
     * @param \stdClass $controlref
     *
     * @return Finding
     */
    public function setControlref($controlref)
    {
        $this->controlref = $controlref;

        return $this;
    }

    /**
     * Get controlref
     *
     * @return \stdClass
     */
    public function getControlref()
    {
        return $this->controlref;
    }
    
    /**
     * Add owners
     *
     * @param \stdClass $user
     *
     * @return Finding
     */
    public function addOwner(User $owner)
    {
	    
        if (!$this->owners->contains($owner)) {
            $this->owners->add($owner);
        }
    }

	/**
     * Removes owners
     *
     * @param \stdClass $user
     *
     * @return Finding
     */
    public function removeOwner(User $owner)
    {
        $this->owners->removeElement($owner);
    }

	/**
     * Get associated owners
     *
     * @return \stdClass
     */
    public function getOwners()
    {
        return $this->owners;
    }
}

