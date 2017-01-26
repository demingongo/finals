<?php

namespace Novice\Form\Event;

use Novice\Form\Form;
use Symfony\Component\HttpFoundation\Request;


class FilterRequestEvent extends Event
{
	private $request;

	public function __construct(Form $form, Request $request)
    {
        parent::__construct($form);
		$this->request = $request;
    }

	public function getRequest()
    {
        return $this->request;
    }
}
