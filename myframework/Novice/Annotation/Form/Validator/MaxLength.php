<?php
namespace Novice\Annotation\Form\Validator;

use Novice\Form\Validator\MaxLengthValidator;

/**
 * Annotation class for @MaxLength().
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 */
class MaxLength extends MaxLengthValidator
{
    use FormValidatorTrait;
}