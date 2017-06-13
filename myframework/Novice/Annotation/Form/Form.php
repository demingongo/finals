<?php
namespace Novice\Annotation\Form;

use Novice\Annotation\ConfigurationAnnotation;

/**
 * Annotation class for @Form().
 *
 * @Annotation
 * @Target({"CLASS"})
 *
 */
class Form extends ConfigurationAnnotation
{
    /** @var string */
    private $value;

	public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
         return $this->value;
    }

    public function getAliasName()
    {
        return 'form';
    }

    public function allowArray()
    {
        return false;
    }
}
