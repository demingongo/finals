<?php
namespace Novice\Annotation\Form\Validator;

use Novice\Form\Validator\NotNullValidator;

/**
 * Annotation class for @NotNull().
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 */
class NotNull extends NotNullValidator
{
    use FormValidatorTrait;
}
