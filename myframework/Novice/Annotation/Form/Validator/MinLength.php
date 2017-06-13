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
}