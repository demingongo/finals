<?php

namespace Rgs\CatalogModule\Entity\Model;

use Novice\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

interface PublishedInterface
{
	
	const PUBLISHED = 1;
	const NOT_PUBLISHED = 0;

    public function setPublished($published);

	public function getPublished();

    public function isPublished();
}
