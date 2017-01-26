<?php

namespace Rgs\CatalogModule\Entity;

use Novice\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use DoctrineExtensions\NestedSet\MultipleRootNode;

/**
 * Categorie
 *
 * @ORM\Table(name="Categorie")
 * @ORM\Entity(repositoryClass="Rgs\CatalogModule\Entity\Repository\CategorieRepository")
 */
class Categorie extends Entity implements MultipleRootNode, Model\PublishedInterface
{

	//const PUBLISHED = 1;
	//const NOT_PUBLISHED = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

	/**
     * @ORM\Column(type="integer")
     */
    private $lft;

    /**
     * @ORM\Column(type="integer")
     */
    private $rgt;

	/**
	 * @ORM\Column(type="integer")
	 */
	private $root;

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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Rgs\CatalogModule\Entity\Article", mappedBy="categorie")
     */
    private $articles;


	
	use Model\DateOnCreateUpdateTrait;

	use Model\PublishedTrait;

	
	
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


	public function getLeftValue() { return $this->lft; }
    public function setLeftValue($lft) { $this->lft = $lft; }

    public function getRightValue() { return $this->rgt; }
    public function setRightValue($rgt) { $this->rgt = $rgt; }

	public function getRootValue() { return $this->root; }
	public function setRootValue($root) { $this->root = $root; }

    public function __toString() { return $this->name; }


    /**
     * Set name
     *
     * @param string $name
     * @return Categorie
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
     * @return Categorie
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
     * @return Categorie
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
     * @return Categorie
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
     * Add article
     *
     * @param Rgs\CatalogModule\Entity\Article $article
     * @return Categorie
     */
    public function addArticle(Article $article)
    {
        $this->articles[] = $article;
		$article->setCategorie($this);

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
		$article->setCategorie(null);
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

	/**
     * Set articles
     *
     * @param \Doctrine\Common\Collections\Collection 
     */
    public function setArticles(\Doctrine\Common\Collections\Collection $articles)
    {
        return $this->articles = $articles;
    }

	public function isNew()
	{
		return !isset($this->id);
	}
}
