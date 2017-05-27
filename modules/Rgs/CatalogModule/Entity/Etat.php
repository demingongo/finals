<?php

namespace Rgs\CatalogModule\Entity;

use Novice\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use DoctrineExtensions\NestedSet\MultipleRootNode;

/**
 * Etat
 *
 * @ORM\Table(name="state")
 * @ORM\Entity(repositoryClass="Rgs\CatalogModule\Entity\Repository\EtatRepository")
 */
class Etat extends Entity implements Model\PublishedInterface
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
     * @ORM\OneToMany(targetEntity="Rgs\CatalogModule\Entity\Article", mappedBy="etat")
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

    /**
     * Set name
     *
     * @param string $name
     * @return Etat
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
     * @return Etat
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
     * @return Etat
     */
    public function addArticle(Article $article)
    {
        $this->articles[] = $article;
		$article->setEtat($this);

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
