<?php

namespace Rgs\CatalogModule\Entity;

use Novice\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Rgs\UserModule\Entity\User;

/**
 * Reservation
 *
 * @ORM\Table(name="reservation_article")
 * @ORM\Entity(repositoryClass="Rgs\CatalogModule\Entity\Repository\ReservationArticleRepository")
 */
class ReservationArticle extends Entity
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
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="unit_price", type="decimal", precision=6, scale=2, nullable=true)
     */
    private $unitPrice;

	/**
     * @var Reservation
	 *
     * @ORM\ManyToOne(targetEntity="Rgs\CatalogModule\Entity\Reservation", inversedBy="reservationArticles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="reservation_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $reservation;

    /**
     * @var Article
	 *
     * @ORM\ManyToOne(targetEntity="Rgs\CatalogModule\Entity\Article")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="article_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $article;
	
	
	/**
     * Constructor
     */
    public function __construct()
    {
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


    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

	public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    /**
     * Get unitPrice
     *
     * @return int 
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }



    public function setReservation(Reservation $reservation)
    {
        $this->reservation = $reservation;

        return $this;
    }

    public function getReservation()
    {
        return $this->reservation;
    }

	public function setArticle(Article $article)
    {
        $this->article = $article;

        return $this;
    }

    public function getArticle()
    {
        return $this->article;
    }

	public function isNew()
	{
		return !isset($this->id);
	}
}
