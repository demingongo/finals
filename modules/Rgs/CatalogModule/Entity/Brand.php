<?php

namespace Rgs\CatalogModule\Entity;

use Novice\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use DoctrineExtensions\NestedSet\MultipleRootNode;

/**
 * Brand
 *
 * @ORM\Table(name="brand")
 * @ORM\Entity(repositoryClass="Rgs\CatalogModule\Entity\Repository\BrandRepository")
 */
class Brand extends Model\AssetEntity
{

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=false, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=128, precision=0, scale=0, nullable=false, unique=true)
	 * @Gedmo\Slug(fields={"name", "id"})
     */
    private $slug;

	/**
     * @var string
     *
     * @ORM\Column(name="logo", type="string", nullable=true)
     */
    private $logo;

	/**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true, unique=false)
     */
    private $url;

	/**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Rgs\CatalogModule\Entity\Article", mappedBy="brand")
     */
    private $articles;
	
	
    /**
     * Constructor
     */
    public function __construct($name = null)
    {
		$this->articles = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Brand
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
     * @return Brand
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
     * Set logo
     *
     * @param string $logo
     * @return Brand
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return string 
     */
    public function getLogo()
    {
        return $this->logo;
    }

	/**
     * Set url
     *
     * @param string $url
     * @return Brand
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
     * Add article
     *
     * @param Rgs\CatalogModule\Entity\Article $article
     * @return Brand
     */
    public function addArticle(Article $article)
    {
        $this->articles[] = $article;
		$article->setBrand($this);

        return $this;
    }

    /**
     * Remove article
     *
     * @param Rgs\CatalogModule\Entity\Article $article
     */
    public function removeArticle(Article $article)
    {
        $this->articles->removeElement($article);
    }

    /**
     * Get articles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getArticles()
    {
        return $this->articles;
    }

	public function isNew()
	{
		return !isset($this->id);
	}
}
