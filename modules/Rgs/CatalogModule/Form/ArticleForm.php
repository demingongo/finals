<?php

namespace Rgs\CatalogModule\Form;

use Rgs\CatalogModule\Entity as ENTITY;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

class ArticleForm
{
    private $id;
 
    private $name;

    private $slug;

    private $teaser;

    private $description;

    private $stock;

    private $prix;

    private $image;

    private $url;
	
    private $category;

    private $etat;

    private $brand;

	
	
	/**
     * Constructor
     */
    public function __construct($name = null)
    {
		$this->setStock(1);
		$this->setPrix(0);

		if(!empty($name)){
			$this->setName($name);
		}
    }
	
	public function setId($id)
    {
        $this->id = $id;
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
     * Set teaser
     *
     * @param string $teaser
     * @return Article
     */
    public function setTeaser($teaser)
    {
        $this->teaser = $teaser;

        return $this;
    }

    /**
     * Get teaser
     *
     * @return string 
     */
    public function getTeaser()
    {
        return $this->teaser;
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

	/**
     * Set stock
     *
     * @param string $stock
     * @return Article
     */
    public function setStock($stock)
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * Get stock
     *
     * @return string 
     */
    public function getStock()
    {
        return $this->stock;
    }

	/**
     * Set prix
     *
     * @param string $prix
     * @return Article
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return string 
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * Set category
     *
     * @param Rgs\CatalogModule\Entity\Category $category
     * @return Article
     */
    public function setCategory(ENTITY\Category $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return Rgs\CatalogModule\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }

	/**
     * Set etat
     *
     * @param Rgs\CatalogModule\Entity\Etat $etat
     * @return Article
     */
    public function setEtat(ENTITY\Etat $etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return Rgs\CatalogModule\Entity\Etat 
     */
    public function getEtat()
    {
        return $this->etat;
    }

	/**
     * Set brand
     *
     * @param Rgs\CatalogModule\Entity\Brand $brand
     * @return Article
     */
    public function setBrand(ENTITY\Brand $brand)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get brand
     *
     * @return Rgs\CatalogModule\Entity\Brand 
     */
    public function getBrand()
    {
        return $this->brand;
    }

	public function isNew()
	{
		return isset($this->id);
	}
}
