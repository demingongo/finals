<?php

namespace Rgs\CatalogModule\Entity;

use Novice\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Rgs\UserModule\Entity\User;

/**
 * Request
 *
 * @ORM\Table(name="request")
 * @ORM\Entity(repositoryClass="Rgs\CatalogModule\Entity\Repository\RequestRepository")
 */
class Request extends Entity implements Model\PublishedInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=128,nullable=false, unique=false)
     */
    private $subject;

	/**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

	/**
     * @var string
     *
     * @ORM\Column(name="image", type="string", nullable=true)
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="Rgs\UserModule\Entity\User", inversedBy="reservations")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

	
	use Model\DateOnCreateUpdateTrait;

	use Model\PublishedTrait;

	
	
	/**
     * Constructor
     */
    public function __construct($name = null)
    {
        $this->setPublished(self::PUBLISHED);

		if(!empty($name)){
			$this->setName($name);
		}
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
     * Set subject
     *
     * @param string $subject
     * @return Article
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string 
     */
    public function getSubject()
    {
        return $this->subject;
    }

	/**
     * Set description
     *
     * @param string $description
     * @return Article
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
     * Set image
     *
     * @param string $image
     * @return Article
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string 
     */
    public function getImage()
    {
        return $this->image;
    }

	/**
     * Set user
     *
     * @param Rgs\UserModule\Entity\User $user
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return Rgs\UserModule\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

	public function isNew()
	{
		return !isset($this->id);
	}
}
