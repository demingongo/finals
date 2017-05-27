<?php

namespace Rgs\CatalogModule\Entity;

use Novice\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Article
 *
 * @ORM\Table(name="article")
 * @ORM\Entity(repositoryClass="Rgs\CatalogModule\Entity\Repository\ArticleRepository")
 */
class Article extends Entity implements Model\PublishedInterface
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64,nullable=false, unique=false)
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
     * @ORM\Column(name="teaser", type="text", nullable=true)
     */
    private $teaser;

	/**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

	/**
     * @var integer
     *
     * @ORM\Column(name="stock", type="integer", length=64,nullable=false, unique=false, options={"default":1})
     */
    private $stock;

	/**
     * @var decimal
     *
     * @ORM\Column(name="prix", type="decimal", precision=6, scale=2, nullable=false, unique=false, options={"default":0})
     */
    private $prix;

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
     * @var Rgs\CatalogModule\Entity\Category
     *
     * @ORM\ManyToOne(targetEntity="Rgs\CatalogModule\Entity\Category", inversedBy="articles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $category;

	/**
     * @var Rgs\CatalogModule\Entity\Etat
     *
     * @ORM\ManyToOne(targetEntity="Rgs\CatalogModule\Entity\Etat", inversedBy="articles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="etat_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $etat;

	/**
     * @var Rgs\CatalogModule\Entity\Marque
     *
     * @ORM\ManyToOne(targetEntity="Rgs\CatalogModule\Entity\Marque", inversedBy="articles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="marque_id", referencedColumnName="id")
     * })
     */
    private $marque;


	
	use Model\DateOnCreateUpdateTrait;

	use Model\PublishedTrait;

	
	
	/**
     * Constructor
     */
    public function __construct($name = null)
    {
		$this->setStock(1);
		$this->setPrix(0);
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
    public function setCategory(Category $category)
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
    public function setEtat(Etat $etat)
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
     * Set marque
     *
     * @param Rgs\CatalogModule\Entity\Marque $marque
     * @return Article
     */
    public function setMarque(Marque $marque)
    {
        $this->marque = $marque;

        return $this;
    }

    /**
     * Get marque
     *
     * @return Rgs\CatalogModule\Entity\Marque 
     */
    public function getMarque()
    {
        return $this->marque;
    }

	public function isNew()
	{
		return !isset($this->id);
	}
}
