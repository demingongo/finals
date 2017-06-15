<?php

namespace Novice\Annotation\Form\Validator;

trait FormValidatorTrait
{
    public function setValue($value)
    {
        $this->setErrorMessage($value);
    }

    public function getValue()
    {
         return $this->errorMessage();
    }
}