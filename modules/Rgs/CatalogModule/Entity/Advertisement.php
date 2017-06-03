<?php

namespace Rgs\CatalogModule\Entity;

use Novice\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Advertisement
 *
 * @ORM\Table(name="advertisement")
 * @ORM\Entity(repositoryClass="Rgs\CatalogModule\Entity\Repository\AdvertisementRepository")
 */
class Advertisement extends Model\AssetEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64,nullable=false, unique=false)
     */
    protected $name;

	/**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=128, precision=0, scale=0, nullable=false, unique=true)
	 * @Gedmo\Slug(fields={"name", "id"})
     */
    protected $slug;


	/**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

	/**
     * @var string
     *
     * @ORM\Column(name="image", type="string", nullable=true)
     */
    private $image;

	/**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255,nullable=true, unique=false)
     */
    private $url;
	
	
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
     * Set name
     *
     * @param string $name
     * @return Article
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
     * Set slug
     *
     * @param string $slug
     * @return Article
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
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
     * Set url
     *
     * @param string $url
     * @return Article
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }
}
