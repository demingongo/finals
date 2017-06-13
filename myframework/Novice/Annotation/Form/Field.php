<?php
namespace Novice\Annotation\Form;

use Novice\Annotation\ConfigurationAnnotation;

/**
 * Annotation class for @Field().
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 */
class Field extends ConfigurationAnnotation
{
    /** @var string */
    private $value;

    /** @var string */
    private $fieldClass;

    /** @var array */
    private $arguments;

	public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
         return $this->value;
    }

    public function setFieldClass($fieldClass)
    {
        $this->fieldClass = $fieldClass;
    }

    public function getFieldClass()
    {
         return $this->fieldClass;
    }

    public function setArguments($arguments)
    {
        $this->arguments = $arguments;
    }

    public function getArguments()
    {
         return $this->arguments;
    }

    public function getAliasName()
    {
        return 'field';
    }

    public function allowArray()
    {
        return false;
    }
}
