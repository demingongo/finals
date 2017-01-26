<?php

namespace Rgs\CatalogModule\Entity\Model;

use Novice\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait PublishedTrait
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="published", type="boolean", nullable=false, unique=false, options={"default":true})
     */
    private $published;


    /**
     * Set published
     *
     * @param boolean $published
     */
    public function setPublished($published)
    {
		if (in_array($published, array(self::PUBLISHED, self::NOT_PUBLISHED, true, false)))
		{
			$this->published = $published;
		}
    }

	/**
     * Get published
     *
     * @return boolean 
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Get published
     *
     * @return boolean 
     */
    public function isPublished()
    {
        return ($this->published == self::PUBLISHED || $this->published == true);
    }
}
