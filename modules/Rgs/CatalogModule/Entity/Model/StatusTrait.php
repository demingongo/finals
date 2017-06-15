<?php

namespace Rgs\CatalogModule\Entity\Model;

use Novice\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait StatusTrait
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=true, unique=false)
     */
    private $status;


    /**
     * Set status
     *
     * @param boolean $status
     */
    public function setStatus($status)
    {
		if (in_array($status, array(self::STATUS_ACTIVE, self::STATUS_ARCHIVED, true, false)))
		{
			$this->status = $status;
		}
    }

	/**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function isActive()
    {
        return ($this->status == self::STATUS_ACTIVE || $this->status == true);
    }

    public function hasStatus()
    {
        return isset($this->status);
    }
}
