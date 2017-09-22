<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Authority
 *
 * @ORM\Table(name="authority")
 * @ORM\Entity(repositoryClass="App\Repository\AuthorityRepository")
 */
class Authority
{
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
     * @ORM\Column(name="title", type="string", length=255,)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="fullname", type="string", length=255)
     */
    private $fullname;

    /**
     * @var string
     *
     * @ORM\Column(name="altnames", type="string", length=255)
     */
    private $altnames;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdate", type="date")
     */
    private $createdate;

    /**
     * @var string
     *
     * @ORM\Column(name="version", type="string", length=20)
     */
    private $version;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=1000, nullable=true)
     */
    private $description;


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
     * @return Authority
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
     * Set fullname
     *
     * @param string $fullname
     *
     * @return Authority
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * Get fullname
     *
     * @return string
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * Set altnames
     *
     * @param string $altnames
     *
     * @return Authority
     */
    public function setAltnames($altnames)
    {
        $this->altnames = $altnames;

        return $this;
    }

    /**
     * Get altnames
     *
     * @return string
     */
    public function getAltnames()
    {
        return $this->altnames;
    }

    /**
     * Set createdate
     *
     * @param \DateTime $createdate
     *
     * @return Authority
     */
    public function setCreatedate($createdate)
    {
        $this->createdate = $createdate;

        return $this;
    }

    /**
     * Get createdate
     *
     * @return \DateTime
     */
    public function getCreatedate()
    {
        return $this->createdate;
    }

    /**
     * Set version
     *
     * @param string $version
     *
     * @return Authority
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Authority
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
     * @param integer $authorityref
     *
     * @return Authority
     */
    public function setAuthorityRef($authorityref)
    {
        $this->authorityref = $authorityref;

        return $this;
    }

    /**
     * Get authorityref
     *
     * @return string
     */
    public function getAuthorityRef()
    {
        return $this->authorityref;
    }
}

