<?php

namespace Rgs\UserModule\Entity;

use Novice\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Rgs\CatalogModule\Entity\Reservation;
use Rgs\CatalogModule\Entity\Model\DateOnCreateUpdateTrait;

/**
 * User
 *
 * @ORM\Table(name="User")
 * @ORM\Entity(repositoryClass="Rgs\UserModule\Entity\Repository\UserRepository")
 */
class User extends Entity implements Model\LockedInterface
{
	const ROLE_DEFAULT = 'ROLE_USER';
    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

	const ACTIVATED = 1;
	const NOT_ACTIVATED = 0;

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
     * @ORM\Column(name="login", type="string", length=255, precision=0, scale=0, nullable=false, unique=true)
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=128, precision=0, scale=0, nullable=false, unique=true)
	 * @Gedmo\Slug(fields={"login", "id"})
     */
    private $slug;

	/**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=50, precision=0, scale=0, nullable=false, unique=true)
     */
    private $email;

	/**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=64, precision=0, scale=0, nullable=false)
     */
    private $password;

	/**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=64, nullable=true)
     */
    private $adresse;

	/**
     * @var string
     *
     * @ORM\Column(name="code_postal", type="string", length=6, nullable=true)
     */
    private $codePostal;

	/**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=24, nullable=true)
     */
    private $ville;

	/**
     * @var string
     *
     * @ORM\Column(name="tel", type="string", length=14, nullable=true)
     */
    private $tel;

	/**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=14, nullable=true)
     */
    private $mobile;

	/**
     * @var string
     *
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     */
    private $lastLogin;

	/**
     * @var boolean
     *
     * @ORM\Column(name="activated", type="boolean", nullable=false, unique=false, options={"default":false})
     */
    private $activated;

	/**
     * @var string
     *
     * @ORM\Column(name="confirmation_token", type="string", length=64, precision=0, scale=0, nullable=true, unique=false)
     */
    private $confirmationToken;

	/**
     * @var Rgs\UserModule\Entity\Group
     *
     * @ORM\ManyToOne(targetEntity="Rgs\UserModule\Entity\Group", inversedBy="users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $group;

	/**
     * @var Rgs\UserModule\Entity\AuthToken
     *
     * @ORM\OneToMany(targetEntity="Rgs\UserModule\Entity\AuthToken", mappedBy="user")
     */
    private $authTokens;

	/**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Rgs\CatalogModule\Entity\Reservation", mappedBy="user")
     */
    private $reservations;

	/**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\Column(name="roles", type="array", nullable=false)
     */
    private $roles;


	
	use DateOnCreateUpdateTrait;
	
	use Model\LockedTrait;

    
	
	/**
     * Constructor
     */
    public function __construct()
    {
		$this->authTokens = new \Doctrine\Common\Collections\ArrayCollection();
		$this->reservations = new \Doctrine\Common\Collections\ArrayCollection();
		$this->setActivated(self::NOT_ACTIVATED);
		$this->setLocked(self::NOT_LOCKED);
		$this->roles = array();
    }

	public function addRole($role)
    {
        $role = strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

	/**
     * Returns the user roles
     *
     * @return array The roles
     */
    public function getRoles()
    {
        $roles = $this->roles;

        /*foreach ($this->getGroup() as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }*/

		$roles = array_merge($roles, $this->getGroup()->getRoles()); //car on ne fait parti que d'un seul group

        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;

        return array_unique($roles);
    }

	/**
     * Never use this to check if this user has access to anything!
     *
     * Use the SecurityContext, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $securityContext->isGranted('ROLE_USER');
     *
     * @param string $role
     *
     * @return boolean
     */
    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

	public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

	public function setRoles(array $roles)
    {
        $this->roles = array();

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

	public function isValid()
    {
        return isset($this->login) && !empty($this->login);
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
     * Set login
     *
     * @param string $login
     * @return User
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login
     *
     * @return string 
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return User
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
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

	/**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

	
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    
    public function getAdresse()
    {
        return $this->adresse;
    }

	public function setCodePostal($codePostal)
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    
    public function getCodePostal()
    {
        return $this->codePostal;
    }

	public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    
    public function getVille()
    {
        return $this->ville;
    }

	public function setTel($tel)
    {
        $this->tel = $tel;

        return $this;
    }

    
    public function getTel()
    {
        return $this->tel;
    }

	
	public function setMobile($mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }

    
    public function getMobile()
    {
        return $this->mobile;
    }

	/**
     * Set lastLogin
     *
     * @param string $date
     * @return $this
     */
    public function setLastLogin(\DateTime $date)
    {
        $this->lastLogin = $date;

        return $this;
    }

    /**
     * Get lastLogin
     *
     * @return \DateTime 
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }
	
	/**
     * Get lastLogin
     *
     * @return string 
     */
    public function getLastLoginToString()
    {
		if(isset($this->lastLogin))
			return $this->lastLogin->format('Y-m-d H:i:s');
    }

	/**
     * Set confirmationToken
     *
     * @param string $confirmationToken
     * @return User
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * Get confirmationToken
     *
     * @return string 
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

	/**
     * Set activated
     *
     * @param boolean $activated
     * @return User
     */
    public function setActivated($activated)
    {
		if (in_array($activated, array(self::ACTIVATED, self::NOT_ACTIVATED, true, false)))
		{
			$this->activated = $activated;
		}

        return $this;
    }

    /**
     * Get activated
     *
     * @return boolean 
     */
    public function isActivated()
    {
        return $this->activated;
    }

    public function getActivated()
    {
        return $this->activated;
    }

	/**
     * Set group
     *
     * @param Rgs\UserModule\Entity\Group $group
     * @return User
     */
    public function setGroup(Group $group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return Rgs\UserModule\Entity\Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    public function addAuthToken(AuthToken $token)
    {
        $this->authTokens[] = $token;
		$token->setUser($this);

        return $this;
    }

	public function removeAuthToken(AuthToken $token)
    {
        $this->authTokens->removeElement($token);
    }

    public function getAuthTokens()
    {
        return $this->authTokens;
    }

	public function addReservation(Reservation $reservation)
    {
        $this->reservations[] = $reservation;
		$reservation->setUser($this);

        return $this;
    }

	public function removeReservation(Reservation $reservation)
    {
        $this->reservations->removeElement($reservation);
    }

    public function getReservations()
    {
        return $this->reservations;
    }

	public function isNew()
	{
		return !isset($this->id);
	}
}
