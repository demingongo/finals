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

class SmartyTemplating extends Smarty implements TemplatingInterface , ContainerAwareInterface
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

	$rdir = $this->container->getParameter('app.root_dir');
	$cdir = $this->container->getParameter('app.cache_dir');

	$resources_dir = '/Resources';
	
	$this->setCompileDir($cdir.'/smarty/templates_c/')
		 ->setCacheDir($cdir.'/smarty/cache/')
		 ->setTemplateDir($rdir.$resources_dir.'/templates/')
		 ->addTemplateDir($rdir.$resources_dir.'/templates/errors/', 'errors')
		 ->setConfigDir($rdir.$resources_dir.'/config/')
		 ->addPluginsDir($this->container->get('app')->getModule('FrameworkModule')->getPath().$resources_dir.'/functions/')
		 ->addPluginsDir($rdir.$resources_dir.'/functions/');

	foreach($this->container->get('app')->getModules() as $name => $module) {
		$this->addTemplateDir($module->getPath().$resources_dir.'/views/', $name);
		$this->addConfigDir($module->getPath().$resources_dir.'/config/', $name);
	}

	if(in_array($this->container->getParameter('app.environment'), array('dev', 'test'))){
		$this->setCompileCheck(self::COMPILECHECK_CACHEMISS);
	}else{
		$this->setCompileCheck(self::COMPILECHECK_OFF);
	}

	if($this->container->getParameter('app.debug') == true) {
		$this->debugging_ctrl = ($_SERVER['SERVER_NAME'] == 'localhost') ? 'URL' : 'NONE';
	}
	
	//Pour pouvoir include des templates (ex: formulaires) dans les tags {block}
	$this->inheritance_merge_compiled_includes = false;

	/*if (file_exists($this->getConfigDir()[0].'novice.conf'))
	$this->configLoad('file:novice.conf'); //configLoad('file','section')*/

  }

  public function setAssets(PackageFactory $packageFactory){

	 $this->assets = $packageFactory->getPackages();
	
	/*$requestStack = new RequestStack(); $requestStack->push($this->container->get('app')->httpRequest());
	$versionStrategy = new StaticVersionStrategy('v64', '%s?version=%s');
	$context = new RequestStackContext($requestStack);
	
	$defaultPackage = new PathPackage('',$versionStrategy, $context);
	$namedPackages = array(
		'img' => new UrlPackage('http://img.example.com/', $versionStrategy, $context),
		'cdnjs' => new UrlPackage('https://cdnjs.cloudflare.com/ajax/libs/', $versionStrategy, $context),
		'css' => new PathPackage('/css', $versionStrategy, $context),
		'js' => new PathPackage('/js', $versionStrategy, $context),
	);

	$this->asset = new Packages($defaultPackage, $namedPackages);*/
  }

  public function getAssets(){
		return $this->assets;
  }

    /**
     * Sets the Container associated with this Controller.
     *
     * @param ContainerInterface $container A ContainerInterface instance
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

	public function setContentFile($contentFile)
   {
    if (!is_string($contentFile) || empty($contentFile))
    {
      throw new \InvalidArgumentException('La vue spécifiée est invalide');
    }
    
    $this->contentFile = $contentFile;
   }

    public function getContainer()
	{
		return $this->container;
	}

  public function getGeneratedPage()
  {
	if( !$this->templateExists($this->contentFile) ){
		throw new \InvalidArgumentException('La vue specifiee n\'existe pas : "'.$this->contentFile.'"');
	}

	$this->display($this->contentFile);    
  }

	/*redefinition de la method registerObject de Smarty pour ne pas enregistrer 2 fois de suite un
	objet avec le meme string_name */
	/*public function registerObject( $object_name, $object, $allowed_methods_properties = null, $format = true, $block_methods = null)
	{
		if(array_key_exists($object_name,$this->registered_objects))
		{
			throw new \RuntimeException('Object "'.$object_name.'" already registered in the template with method "registerObject()" (Smarty). You must unregister it to register a new object with the name "'.$object_name.'".');
		}
		parent::registerObject( $object_name, $object, $allowed_methods_properties, $format, $block_methods);
	}*/

	/*redefinition de la method getRegisteredObject de Smarty qui renvoyer NULL si aucun objet
	n'a été enregistré à ce nom avec registerObject */
	/*public function getRegisteredObject( $object_name)
	{
		$retour = null;
		if(array_key_exists($object_name,$this->registered_objects))
		{
			$retour = parent::getRegisteredObject( $object_name);
		}
		return $retour;
	}*/

	
}