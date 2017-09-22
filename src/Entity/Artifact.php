<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Controls
 *
 * @ORM\Table(name="artifacts")
 * @ORM\Entity(repositoryClass="App\Repository\ArtifactRepository")
 */
class Artifact
{
	// Number of artifacts to display by default
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
     * @ORM\Column(name="filename", type="string", length=255)
     */
    private $filename;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdate", type="date")
     */
    private $uploaddate;
    
    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="userref", referencedColumnName="id")
     */
    private $uploaduser;

    /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string", length=20, nullable=true)
     */
    private $extension;
    
    /**
     * @ORM\Column(type="string")
     *
     * @Assert\File(mimeTypes={ "application/pdf" })
     */
    private $file;


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
     * @param string $filename
     *
     * @return Artifact
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set uploaddate
     *
     * @param \DateTime $uploaddate
     *
     * @return Artifact
     */
    public function setUploadDate($uploaddate)
    {
        $this->uploaddate = $uploaddate;

        return $this;
    }

    /**
     * Get uploaddate
     *
     * @return \DateTime
     */
    public function getUploadDate()
    {
        return $this->uploaddate;
    }
    
    /**
     * Set uploaduser
     *
     * @param User $uploaduser
     *
     * @return Artifact
     */
    public function setUploadUser($uploaduser)
    {
        $this->uploaduser = $uploaduser;

        return $this;
    }

    /**
     * Get uploaduser
     *
     * @return User
     */
    public function getUploadUser()
    {
        return $this->uploaduser;
    }

    /**
     * Set extension
     *
     * @param string $extension
     *
     * @return Artifact
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }
    
    /**
     * Set file
     *
     * @param string $file
     *
     * @return Artifact
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }
}

