<?php

namespace Rgs\CatalogModule\Entity\Model;

use Novice\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait DateOnUpdateTrait
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", precision=0, scale=0, nullable=true, unique=false)
	 * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;


    /**
     * Set updated
     *
     * @param \DateTime $date
     * @return Article
     */
    public function setUpdatedAt($date)
    {
        $this->updatedAt = $date;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

	/**
     * Get updated
     *
     * @return string 
     */
    public function getUpdatedAtToString()
    {
		if(isset($this->updatedAt))
			return $this->updatedAt->format('Y-m-d H:i:s');
    }
}
