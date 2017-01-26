<?php
namespace Novice\Annotation;

/**
 * Annotation class for @Assign().
 *
 * @Annotation
 * @Target({"METHOD"})
 *
 */
class Assign
{
    private $value;
    private $name;
    private $nocache = false;

	private $routeNames;
	
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

    public function setNocache($nocache)
    {
        $this->nocache = $nocache;
    }

    public function getNocache()
    {
        return $this->nocache;
    }

	public function setRoutenames($name)
    {
		if(!is_array($name)){
			$name = array($name);
		}
        $this->routeNames = $name;
    }

    public function getRoutenames()
    {
        return $this->routeNames;
    }
}
