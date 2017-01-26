<?php
namespace Novice\Annotation;

/**
 * Annotation class for @Template().
 *
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 *
 */
class Template extends ConfigurationAnnotation
{
    private $template;
    
	protected $vars = array();

	protected $cache_id;

	protected $compile_id;
	
	/**
     * The controller (+action) this annotation is set to.
     *
     * @var array
     */
    private $owner;

	public function setValue($value)
    {
        $this->setTemplate($value);
    }

    public function getValue()
    {
        return $this->getTemplate();
    }

	public function setTemplate($template)
    {
        $this->template = $template;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function setVars($vars)
    {
        $this->vars = $vars;
    }

    public function getVars()
    {
        return $this->vars;
    }

	public function setCacheid($cache_id)
    {
        $this->cache_id = $cache_id;
    }

    public function getCacheid()
    {
        return $this->cache_id;
    }

	public function setCompileid($compile_id)
    {
        $this->compile_id = $compile_id;
    }

    public function getCompileid()
    {
        return $this->compile_id;
    }
	
    public function getAliasName()
    {
        return 'template';
    }

    public function allowArray()
    {
        return false;
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
