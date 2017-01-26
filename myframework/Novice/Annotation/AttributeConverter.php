<?php
namespace Novice\Annotation;

/**
 * Annotation class for @Template().
 *
 * @Annotation
 * @Target({"METHOD"})
 *
 */
class AttributeConverter extends ConfigurationAnnotation
{

	const REQUEST = "request";

	const QUERY = "query";

	const ATTRIBUTES = "attributes";

    private $name;

	private $from;
    
	private $class;
	
	private $prefix;
	
	private $editor;
	
	/**
     * The controller (+action) this annotation is set to.
     *
     * @var array
     */
    private $owner;

	public function setValue($value)
    {
        $this->setName($value);
    }

    public function getValue()
    {
        return $this->getName();
    }

	public function setName($name)
    {
        $this->name = $name;
    }

	public function getName()
    {
        return $this->name;
    }

    public function getFrom()
    {
        return $this->from;
    }

	public function setFrom($from)
    {
        $this->from = $from;
    }    

    public function setClass($class)
    {
        $this->class = $class;
    }

    public function getClass()
    {
        return $this->class;
    }
	
	public function getPrefix()
    {
        return $this->prefix;
    }

	public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }
	
	public function setEditor($editor)
    {
        $this->editor = $editor;
    }

    public function getEditor()
    {
        return $this->editor;
    }
	
    public function getAliasName()
    {
        return 'attribute_converter';
    }

    public function allowArray()
    {
        return true;
    }
	
	/**
     * @param array $owner
     */
    public function setOwner(array $owner)
    {
        $this->owner = $owner;
    }

    /**
     * The controller (+action) this annotation is attached to.
     *
     * @return array
     */
    public function getOwner()
    {
        return $this->owner;
    }
}
