<?php

namespace Novice\Form\Event;

use Symfony\Component\EventDispatcher\Event as BaseEvent;

use Novice\Form\Form;
use Novice\Entity\Entity;


abstract class Event extends BaseEvent
{
    private $form;

    public function __construct(Form $form)
    {
        $this->form=$form;
    }

    public function getForm()
    {
        return $this->form;
    }
}
