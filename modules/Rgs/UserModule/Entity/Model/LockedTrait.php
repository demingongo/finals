<?php

namespace Rgs\UserModule\Entity\Model;

use Novice\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait LockedTrait
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="locked", type="boolean", nullable=false, unique=false, options={"default":false})
     */
    private $locked;


    /**
     * Set locked
     *
     * @param boolean $locked
     */
    public function setLocked($locked)
    {
		if (in_array($locked, array(self::LOCKED, self::NOT_LOCKED, true, false)))
		{
			$this->locked = $locked;
		}

        return $this;
    }

    /**
     * Get locked
     *
     * @return boolean 
     */
    public function isLocked()
    {
        return ($this->locked == self::LOCKED || $this->locked == true);
    }

	/**
     * Get locked
     *
     * @return boolean 
     */
    public function getLocked()
    {
        return $this->locked;
    }
}
