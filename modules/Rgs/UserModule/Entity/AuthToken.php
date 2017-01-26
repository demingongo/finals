<?php

namespace Rgs\UserModule\Entity;

use Novice\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * AuthToken
 *
 * @ORM\Table(name="auth_token")
 * @ORM\Entity(repositoryClass="Rgs\UserModule\Entity\Repository\AuthTokenRepository")
 */
class AuthToken extends Entity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=64, precision=0, scale=0, nullable=false, unique=false)
     */
    private $token;

    /**
     * @ORM\ManyToOne(targetEntity="Rgs\UserModule\Entity\User", inversedBy="authTokens")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

	/**
     * @var datetime
     *
     * @ORM\Column(name="expires_at", type="datetime")
     */
    private $expiresAt;

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
     * Set token
     *
     * @param string $token
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
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

	public function isNew()
	{
		return !isset($this->id);
	}
}
