<?php

namespace Novice\Form\Extension;

use Novice\Form\Form;

abstract class Extension implements ExtensionInterface
{
  protected $form;
  
  protected $formName;

  public function setForm(Form $form)
  {
    $this->form = $form;
  }
}