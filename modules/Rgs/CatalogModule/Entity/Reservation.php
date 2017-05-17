<?php

namespace Rgs\CatalogModule\Entity;

use Novice\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Rgs\UserModule\Entity\User;

/**
 * Reservation
 *
 * @ORM\Table(name="reservation")
 * @ORM\Entity(repositoryClass="Rgs\CatalogModule\Entity\Repository\ReservationRepository")
 */
class Reservation extends Entity
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
     * @ORM\Column(name="reduction", type="smallint", nullable=true)
     */
    private $reduction;

	/**
     * @var \DateTime
     *
     * @ORM\Column(name="expires_at", type="datetime", nullable=false)
     */
    private $expiresAt;

	/**
     * @ORM\ManyToOne(targetEntity="Rgs\UserModule\Entity\User", inversedBy="reservations")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

	/** @ORM\OneToMany(targetEntity="Rgs\CatalogModule\Entity\ReservationArticle", mappedBy="reservation", cascade={"persist","remove"}) */
    protected $reservationArticles;
		
	use Model\DateOnCreateTrait;
	
	
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

	/**
     * Set reduction
     *
     * @param integer $reduction
     * @return Reservation
     */
    public function setReduction($reduction)
    {
        $this->reduction = $reduction;

        return $this;
    }

    /**
     * Get reduction
     *
     * @return int 
     */
    public function getReduction()
    {
        return $this->reduction;
    }

	/**
     * Set expiresAt
     *
     * @param string $date
     * @return $this
     */
    public function setExpiresAt(\DateTime $date)
    {
        $this->expiresAt = $date;

        return $this;
    }

    /**
     * Get expiresAt
     *
     * @return string 
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Set user
     *
     * @param Rgs\UserModule\Entity\User $user
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return Rgs\UserModule\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

	public function addReservationArticle(ReservationArticle $reservationArticle)
    {
        $this->reservationArticles[] = $reservationArticle;
		$reservationArticle->setReservation($this);

        return $this;
    }

	public function removeReservationArticle(ReservationArticle $reservationArticle)
    {
        $this->reservationArticles->removeElement($reservationArticle);
    }

    public function getReservationArticles()
    {
        return $this->reservationArticles;
    }

	public function isNew()
	{
		return !isset($this->id);
	}
}
