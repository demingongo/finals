<?php

namespace Novice\Annotation;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;

use Symfony\Component\DependencyInjection\ContainerAware;


class TemplateResolver extends ContainerAware
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var string
     */
    protected $templateAnnotationClass = 'Novice\\Templating\\Annotation\\Template';

    /**
     * @var int
     */
    protected $defaultRouteIndex = 0;
	
	protected $templating;
	
	
	/**
     * Constructor.
     *
     * @param Reader $reader
     */
    public function __construct(/*Reader $reader*/)
    {
        //$this->reader = $reader;
		$this->reader = new \Doctrine\Common\Annotations\AnnotationReader();
    }
	
	
	public function setTemplating(\Novice\Templating\TemplatingInterface $templating)
    {
		$this->templating = $templating;
    }

    /**
     * Sets the annotation class to read route properties from.
     *
     * @param string $class A fully-qualified class name
     */
    /*public function setTemplateAnnotationClass($class)
    {
        $this->templateAnnotationClass = $class;
    }*/
	
	
	public function load($objet)
    {
        if (!is_object($objet)) {
            //throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
			return;
        }

        $class = new \ReflectionObject($objet);
        if ($class->isAbstract()) {
            throw new \InvalidArgumentException(sprintf('Annotations from class "%s" cannot be read as it is abstract.', $class));
        }

        $globals = $this->getTemplatingAssignGlobals($class);

        /*$collection = new RouteCollection();
        $collection->addResource(new FileResource($class->getFileName()));*/

        foreach ($class->getMethods() as $method) {
            $this->defaultRouteIndex = 0;
            foreach ($this->reader->getMethodAnnotations($method) as $annot) {
                if ($annot instanceof $this->templateAnnotationClass) {
                    $this->assign($annot, $globals, $class, $method, $objet);
                }
            }
        }

        //return $collection;
    }
	
	

    protected function assign($annot, $globals, \ReflectionObject $class, \ReflectionMethod $method, $objet)
    {
		
        if (null !== $template = $annot->getTemplate()) {
                $template = $globals['template'];
        }
			
		
		$args = array();
        foreach ($method->getParameters() as $param) {
			if($this->container->has($param->getName())){
				$args[] = $this->container->get($param->getName());
			}
            else if (/*!isset($defaults[$param->getName()]) && */$param->isDefaultValueAvailable()) {
                //$defaults[$param->getName()] = $param->getDefaultValue();
            }
			else{
				throw new \Exception("Cannot use annotation @Templating for METHOD with PARAMETERS with no default value,
				 except if parameter is service");
			}
        }
		
		$var = $method->invokeArgs($objet, $args);
		
		$nocache = $annot->getNocache();

        if (null === $nocache) {
            $nocache = $globals['nocache'];
        }
		
		/** todo **/
		$this->templating->assign($name, $var, $nocache);
		
		/*dump($var);
		dump($method);
		dump($class);
		exit(__METHOD__);*/

        //$route = $this->createRoute($globals['path'].$annot->getPath(), $defaults, $requirements, $options, $host, $schemes, $methods, $condition);

        //$this->configureRoute($route, $class, $method, $annot);
		
		
        
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && preg_match('/^(?:\\\\?[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)+$/', $resource) && (!$type || 'annotation' === $type);
    }

    /**
     * {@inheritdoc}
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getResolver()
    {
    }

    /**
     * Gets the default route name for a class method.
     *
     * @param \ReflectionClass  $class
     * @param \ReflectionMethod $method
     *
     * @return string
     */
    protected function getDefaultVarName(\ReflectionObject $class, \ReflectionMethod $method)
    {
        $name = strtolower(str_replace('\\', '_', $class->name).'_'.$method->name);
        if ($this->defaultRouteIndex > 0) {
            $name .= '_'.$this->defaultRouteIndex;
        }
        ++$this->defaultRouteIndex;

        return $name;
    }

    protected function getTemplatingAssignGlobals(\ReflectionObject $class)
    {
        $globals = array(
            'template' => null,
            'vars' => array(),
			'cache_id' => null,
            'compile_id' => null,
        );

        if ($annot = $this->reader->getClassAnnotation($class, $this->templateAnnotationClass)) {
            if (null !== $annot->getTemplate()) {
                $globals['template'] = $annot->getTemplate();
            }

            if (null !== $annot->getVars()) {
                $globals['vars'] = $annot->getVars();
            }
			
			if (null !== $annot->getCacheid()) {
                $globals['cache_id'] = $annot->getCacheid();
            }

            if (null !== $annot->getCompileid()) {
                $globals['compile_id'] = $annot->getCompileid();
            }
        }

        return $globals;
    }

    protected function createRoute($path, $defaults, $requirements, $options, $host, $schemes, $methods, $condition)
    {
        return new Route($path, $defaults, $requirements, $options, $host, $schemes, $methods, $condition);
    }

    //abstract protected function configureRoute(Route $route, \ReflectionClass $class, \ReflectionMethod $method, $annot);
}
