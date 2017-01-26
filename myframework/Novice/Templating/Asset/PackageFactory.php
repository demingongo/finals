<?php
namespace Novice\Templating\Asset;

use Symfony\Component\DependencyInjection\ContainerInterface,
	Symfony\Component\DependencyInjection\ContainerAwareTrait,
	Symfony\Component\DependencyInjection\ContainerAwareInterface;

use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\UrlPackage;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\Asset\VersionStrategy\StaticVersionStrategy,
	Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PackageFactory implements ContainerAwareInterface
{ 

	use ContainerAwareTrait;
    /**
     * @var ContainerInterface
     *
     * @api
     */

  private $versionStrategy;
  private $defaultPath;
  private $paths;
  private $urls;

  private $context;

  private $packages;

  public function __construct(ContainerInterface $container, VersionStrategyInterface $versionStrategy, $defaultPath = "/", array $paths = array(), array $urls = array())
  {
	$this->setContainer($container);

	$this->defaultPath = $defaultPath;

	$this->paths = $paths;

	$this->urls = $urls;

	$this->versionStrategy = $versionStrategy;
  }

  public function createPackages(){
	
	/*$requestStack = new RequestStack(); 
	$requestStack->push($this->container->get('app')->httpRequest());*/
	
	$requestStack = $this->container->get('request_stack');
	
	$this->context = new RequestStackContext($requestStack);
		
	$defaultPackage = new PathPackage($this->defaultPath,$this->versionStrategy, $this->context);

	$namedPackages = array();
	foreach($this->urls as $id => $urls)
	{
		$namedPackages[$id] = new UrlPackage($urls, $this->versionStrategy, $this->context);
	}
	foreach($this->paths as $id => $path)
	{
		$namedPackages[$id] = new PathPackage($path, $this->versionStrategy, $this->context);
	}

	$this->packages = new Packages($defaultPackage, $namedPackages);
  }

  public function getPackages(){
		return $this->packages;
  }

  public function getContainer(){
		return $this->container;
  }

  public function getBasePath(){
		return $this->context->getBasePath();
  }

  public function getContext(){
		return $this->context;
  }

}