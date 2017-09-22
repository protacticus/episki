<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Controls
 *
 * @ORM\Table(name="controls")
 * @ORM\Entity(repositoryClass="App\Repository\ControlsRepository")
 */
class Controls
{
	// Number of controls to display by default
	const NUM_ITEMS = 10;
	
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
     * @ORM\Column(name="number", type="string", length=255)
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(name="requirement", type="string", length=510)
     */
    private $requirement;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=1000, nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Authority")
     * @ORM\JoinColumn(name="authorityref", referencedColumnName="id")
     */
    private $authorityref;
    
        /**
     * @var User[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\User", cascade={"persist"})
     * @ORM\JoinTable(name="control_owners")
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
     * Set number
     *
     * @param string $number
     *
     * @return Controls
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set requirement
     *
     * @param string $requirement
     *
     * @return Controls
     */
    public function setRequirement($requirement)
    {
        $this->requirement = $requirement;

        return $this;
    }

    /**
     * Get requirement
     *
     * @return string
     */
    public function getRequirement()
    {
        return $this->requirement;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Controls
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
     * Set authorityref
     *
     * @param \stdClass $authorityref
     *
     * @return Controls
     */
    public function setAuthorityref($authorityref)
    {
        $this->authorityref = $authorityref;

        return $this;
    }

    /**
     * Get authorityref
     *
     * @return \stdClass
     */
    public function getAuthorityref()
    {
        return $this->authorityref;
    }
    
    /**
     * Add owners
     *
     * @param \stdClass $user
     *
     * @return Controls
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
     * @return Controls
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

