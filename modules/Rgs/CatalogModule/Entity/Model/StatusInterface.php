<?php

namespace Rgs\CatalogModule\Entity\Model;

use Novice\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

interface StatusInterface
{
	
	const STATUS_ACTIVE = 1;
	const STATUS_ARCHIVED = 0;

    public function setStatus($status);

	public function getStatus();

    public function isActive();

	public function hasStatus();
}
