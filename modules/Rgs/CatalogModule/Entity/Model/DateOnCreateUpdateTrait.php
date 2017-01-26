<?php

namespace Rgs\CatalogModule\Entity\Model;

use Novice\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait DateOnCreateUpdateTrait
{	
	use DateOnCreateTrait;
	use DateOnUpdateTrait;
}
