<?php

namespace Rgs\CatalogModule\Entity\Model;

use Novice\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait DateOnCreateTrait
{
	
	/**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", precision=0, scale=0, nullable=false, unique=false)
	 * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;


    /**
     * Set created
     *
     * @param \DateTime $date
     * @return Article
     */
    public function setCreatedAt($date)
    {
        $this->createdAt = $date;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

	/**
     * Get created
     *
     * @return string 
     */
    public function getCreatedAtToString()
    {
		if(isset($this->createdAt))
			return $this->createdAt->format('Y-m-d H:i:s');
    }
}
