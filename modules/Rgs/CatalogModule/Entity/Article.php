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
class Article extends Model\AssetEntity
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
     * @var Rgs\CatalogModule\Entity\State
     *
     * @ORM\ManyToOne(targetEntity="Rgs\CatalogModule\Entity\State", inversedBy="articles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="state_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $state;

	/**
     * @var Rgs\CatalogModule\Entity\Brand
     *
     * @ORM\ManyToOne(targetEntity="Rgs\CatalogModule\Entity\Brand", inversedBy="articles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="brand_id", referencedColumnName="id")
     * })
     */
    private $brand;

	
	
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
     * Set state
     *
     * @param Rgs\CatalogModule\Entity\State $state
     * @return Article
     */
    public function setState(State $state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return Rgs\CatalogModule\Entity\State 
     */
    public function getState()
    {
        return $this->state;
    }

	/**
     * Set brand
     *
     * @param Rgs\CatalogModule\Entity\Brand $brand
     * @return Article
     */
    public function setBrand($brand)
    {
        if($brand instanceof Brand){
            $this->brand = $brand;
        }
        else if($brand == null){
            $this->brand = null;
        }
        else{
            throw new \Symfony\Component\Debug\Exception\ContextErrorException(
                "Argument 1 passed to Rgs\CatalogModule\Entity\Article::setBrand() must be an instance of Rgs\CatalogModule\Entity\Brand or null, ".gettype($brand)." given"
                );
        }

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
}
