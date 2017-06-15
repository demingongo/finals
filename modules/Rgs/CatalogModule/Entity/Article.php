<?php

namespace Rgs\CatalogModule\Entity;

use Novice\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Novice\Annotation as NOVICE;
use Novice\Annotation\Form\Form as FORM;
use Novice\Annotation\Form\Field as FIELD;
use Novice\Annotation\Form\Extension as EXTENSION;
use Novice\Annotation\Form\Validator as VALIDATOR;

/**
 * Article
 *
 * @ORM\Table(name="article")
 * @ORM\Entity(repositoryClass="Rgs\CatalogModule\Entity\Repository\ArticleRepository")
 * @FORM("article_form")
 */
class Article extends Model\AssetEntity
{

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64,nullable=false, unique=false)
     * @FIELD(fieldClass="Novice\Form\Field\InputField", 
     *                      arguments={"maxlength": 64, "required": true, "control_label": true, "label": "Name", "pattern": "^[^ ].{1,}$",
     *                                  "attributes": {"data-error": "something to write", "title": "Choisir un titre"} 
     *                      }
     *                    )
     * @VALIDATOR\NotNull("Spécifiez le titre")
     * @VALIDATOR\MaxLength(maxLength=64, errorMessage="Le nom spécifié est trop long (64 caractères maximum)")
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
     * @FIELD(fieldClass="Novice\Form\Field\TextareaField", 
     *                      arguments={"title": "Teaser text", "control_label": true, "label": "teaser"}
     *                    )
     */
    private $teaser;

	/**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @FIELD(fieldClass="Novice\Form\Field\TextareaField", 
     *                      arguments={"control_label": true, "label": "Description"}
     *                    )
     */
    private $description;

	/**
     * @var integer
     *
     * @ORM\Column(name="stock", type="integer", length=64,nullable=false, unique=false, options={"default":1})
     * @FIELD(fieldClass="Novice\Form\Field\InputField", 
     *                      arguments={"type": "number", "label": "Stock", "min": 0, "max": 9999,
     *                                  "title": "le nombre de pièces en stock (entre 0 et 9999)",
     *                                  "pattern": "[0-9]{0,}", "placeholder": "0"
     *                      }
     *                    )
     */
    private $stock;

	/**
     * @var decimal
     *
     * @ORM\Column(name="price", type="decimal", precision=6, scale=2, nullable=false, unique=false, options={"default":0})
     * @FIELD(fieldClass="Novice\Form\Field\InputField", 
     *                      arguments={"type": "number", "label": "Prix unitaire", "min": 0, "max": 9999.99, "step": 0.01,
     *                                  "title": "Prix de l'article (en euro), utiliser le point ( . ) pour la virgule",
     *                                  "pattern": "[,.0-9]{0,}", "placeholder": "0.00", "addon": "&euro;"
     *                      }
     *                    )
     */
    private $price;

	/**
     * @var string
     *
     * @ORM\Column(name="image", type="string", nullable=true)
     * @EXTENSION(providerService="ext_provider.image")
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
		$this->setPrice(0);
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
     * Set price
     *
     * @param string $price
     * @return Article
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string 
     */
    public function getPrice()
    {
        return $this->price;
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
