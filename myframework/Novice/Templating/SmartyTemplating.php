<?php
namespace Novice\Templating;

use \Smarty as Smarty,
	Symfony\Component\DependencyInjection\ContainerInterface,
	Symfony\Component\DependencyInjection\ContainerAwareInterface;

/*use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\UrlPackage;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\Asset\VersionStrategy\StaticVersionStrategy;
use Symfony\Component\HttpFoundation\RequestStack;*/
use Novice\Templating\Asset\PackageFactory;
use Novice\Templating\Assignor\AssignorInterface;

class SmartyTemplating extends Smarty implements TemplatingInterface
{

    /**
     * @var ContainerInterface
     *
     * @api
     */
    protected $container;
	protected $contentFile;
	protected $assets;

  public function __construct(ContainerInterface $container)
  {
    parent::__construct();

	$this->setContainer($container);

	if($this->container->getParameter('app.debug') == true) {
		$this->debugging_ctrl = ($_SERVER['SERVER_NAME'] == 'localhost') ? 'URL' : 'NONE';
	}
	
	//Pour pouvoir include des templates (ex: formulaires) dans les tags {block}
	$this->inheritance_merge_compiled_includes = false;

	/*if (file_exists($this->getConfigDir()[0].'novice.conf'))
	$this->configLoad('file:novice.conf'); //configLoad('file','section')*/

  }

  public function setAssets(PackageFactory $packageFactory){

	  //dump($this); exit(__METHOD__);
	 $this->assets = $packageFactory->getPackages();
  }

  public function getAssets(){
		return $this->assets;
  }

	public function setContentFile($contentFile)
   {
    if (!is_string($contentFile) || empty($contentFile))
    {
      throw new \InvalidArgumentException('La vue spécifiée est invalide');
    }
    
    $this->contentFile = $contentFile;
   }

    public function getContentFile()
	{
		return $this->contentFile;
	}

    public function getContainer()
	{
		return $this->container;
	}
	
	public function setContainer(ContainerInterface $container)
	{
		$this->container = $container;
	}

  public function getGeneratedPage()
  {
	if( !$this->templateExists($this->contentFile) ){
		throw new \InvalidArgumentException('La vue specifiee n\'existe pas : "'.$this->contentFile.'"');
	}

	$this->display($this->contentFile, /* $cache_id */ null, /* $compile_id */ null, /* $parent */ null);    
  }
  
  public function assign($varname, $var = null, $nocache = false)
  {
	  if(is_object($varname) && $varname instanceof AssignorInterface){
		  $assignment = $varname->getVarname();
		  if(is_string($assignment)){
			  $var = $varname;
		  }
		  else if(!is_array($assignment)){
			  $r = new \ReflectionObject($varname);
			  throw new \ErrorException(
			  	sprintf('Method "%s" must return a string or an array (%s given).',$r->getName()."::getAssignment()", $this->varToString($assignment))
			  );
		  }

		return parent::assign($assignment, $var, $nocache);
	  }
	  return parent::assign($varname, $var, $nocache);		
  }
  
  private function varToString($var)
    {
        if (is_object($var)) {
            return sprintf('Object(%s)', get_class($var));
        }

        if (is_array($var)) {
            $a = array();
            foreach ($var as $k => $v) {
                $a[] = sprintf('%s => %s', $k, $this->varToString($v));
            }

            return sprintf("Array(%s)", implode(', ', $a));
        }

        if (is_resource($var)) {
            return sprintf('Resource(%s)', get_resource_type($var));
        }

        if (null === $var) {
            return 'null';
        }

        if (false === $var) {
            return 'false';
        }

        if (true === $var) {
            return 'true';
        }

        return (string) $var;
    }
  
  /*public function fetch($template = null, $cache_id = null, $compile_id = null, $parent = null, $display = false, $merge_tpl_vars = true, $no_output_filter = false)
    {
        if ($cache_id !== null && is_object($cache_id)) {
            $parent = $cache_id;
            $cache_id = null;
        }
        if ($parent === null) {
            $parent = $this;
        }
        // get template object
        $_template = is_object($template) ? $template : $this->createTemplate($template, $cache_id, $compile_id, $parent, false);
        // set caching in template object
        $_template->caching = $this->caching;
        // fetch template content
        return $_template->render(true, false, $display);
    }*/
	public function addExtension(Extension\TemplatingExtensionInterface $extension, $cacheable = true, $cache_attrs = array())
	{
		$this->registerPlugin($extension->getType(),$extension->getName(),$extension->getCallback(),$cacheable,$cache_attrs);
	}
}