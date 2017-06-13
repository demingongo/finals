<?php

namespace Novice\Annotation\Form\Validator;

trait FormValidatorTrait
{
    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            $method = 'set'.str_replace('_', '', $key);
            if (!method_exists($this, $method)) {
                throw new \BadMethodCallException(sprintf('Unknown property "%s" on annotation "%s".', $key, get_class($this)));
            }
            $this->$method($value);
        }
    }

    public function setValue($value)
    {
        $this->setErrorMessage($value);
    }

    public function getValue()
    {
         return $this->errorMessage();
    }
}