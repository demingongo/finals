<?php

namespace Rgs\CatalogModule\Entity;

use Novice\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use DoctrineExtensions\NestedSet\MultipleRootNode;

/**
 * State
 *
 * @ORM\Table(name="state")
 * @ORM\Entity(repositoryClass="Rgs\CatalogModule\Entity\Repository\StateRepository")
 */
class State extends Model\AssetEntity
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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Rgs\CatalogModule\Entity\Article", mappedBy="state")
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
     * @return State
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
     * @return State
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
     * Add article
     *
     * @param Rgs\CatalogModule\Entity\Article $article
     * @return State
     */
    public function addArticle(Article $article)
    {
        $this->articles[] = $article;
		$article->setState($this);

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
