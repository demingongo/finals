<?php

namespace Rgs\CatalogModule\Entity\Model;

use Novice\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
* Extends this absctract class to have common fields for asset type of entities :
* - id
* - name
* - slug
* - created_at
* - updated_at
* - published
*/



abstract class AssetEntity extends Entity implements PublishedInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
	
	/**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", precision=0, scale=0, nullable=false, unique=false)
	 * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", precision=0, scale=0, nullable=true, unique=false)
	 * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;

	/**
     * @var boolean
     *
     * @ORM\Column(name="published", type="boolean", nullable=false, unique=false, options={"default":true})
     */
    protected $published;


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

    public function isNew()
	{
		return !isset($this->id);
	}

    abstract public function setName($name);

    abstract public function getName();
}
