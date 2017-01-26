<?php

namespace Rgs\UserModule\Entity\Model;

use Novice\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

interface LockedInterface
{
	
	const LOCKED = 1;
	const NOT_LOCKED = 0;

    public function setLocked($locked);
	public function getLocked();

    public function isLocked();
}
