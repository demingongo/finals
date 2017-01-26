<?php

namespace Rgs\UserModule\Entity;

use Novice\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Group
 *
 * @ORM\Table(name="groups")
 * @ORM\Entity(repositoryClass="Rgs\UserModule\Entity\Repository\Group")
 */
class Group extends Entity implements Model\LockedInterface
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
     * @ORM\Column(name="name", type="string", length=64, precision=0, scale=0, nullable=false, unique=true)
     */
    private $name;

    /**
     * @ORM\Column(name="roles", type="array", nullable=false, unique=false)
     */
    private $roles;

	/**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Rgs\UserModule\Entity\User", mappedBy="group")
     */
    private $users;


	
	use Model\LockedTrait;

    
	
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
		$this->roles = array();
		$this->setLocked(self::NOT_LOCKED);
    }

	/**
     * @param string $role
     *
     * @return Group
     */
    public function addRole($role)
    {
        if (!$this->hasRole($role)) {
            $this->roles[] = strtoupper($role);
        }

        return $this;
    }

	/**
     * @param array $roles
     *
     * @return Group
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

	/**
     * @param string $role
     */
    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->roles, true);
    }

    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param string $role
     *
     * @return Group
     */
    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
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
     * @return Group
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
     * Set level
     *
     * @param int $level
     * @return $this
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return int 
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Add user
     *
     * @param Rgs\UserModule\Entity\User $user
     * @return Categorie
     */
    public function addUser(User $user)
    {
        $this->users[] = $user;
		$article->setGroup($this);

        return $this;
    }

    /**
     * Remove user
     *
     * @param Rgs\UserModule\Entity\User $user
     */
    public function removeUser(User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

	public function isNew()
	{
		return !isset($this->id);
	}
}
