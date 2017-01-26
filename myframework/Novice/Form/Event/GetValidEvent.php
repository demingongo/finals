<?php

namespace Novice\Form\Event;

use Novice\Form\Form;
use Symfony\Component\HttpFoundation\Request;


class GetValidEvent extends Event
{
	private $valid;

	public function __construct(Form $form)
    {
        parent::__construct($form);
		$this->setValid(true);
    }

	public function setValid($valid)
    {
        $this->valid = (bool) $valid;
    }

	public function getValid()
    {
        return $this->valid;
    }
}
