<?php
namespace Novice\Annotation\Form\Validator;

use Novice\Form\Validator\MinLengthValidator;

/**
 * Annotation class for @MinLength().
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 */
class MinLength extends MinLengthValidator
{
    use FormValidatorTrait;

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
}