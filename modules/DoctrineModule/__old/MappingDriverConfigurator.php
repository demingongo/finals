<?php
namespace DoctrineModule;

use Doctrine\Common\Annotations\Reader,
	//Doctrine\Common\Annotations\AnnotationReader,
	Doctrine\Common\Annotations\CachedReader,
	/*Doctrine\Common\Annotations\FileCacheReader,
	Doctrine\Common\Annotations\IndexedReader,
	Doctrine\Common\Annotations\SimpleAnnotationReader,
	Doctrine\ORM\Mapping\Driver\AnnotationDriver,*/
	Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;

class MappingDriverConfigurator
{
  private $cachedAnnotationReader;

  public function __construct(CachedReader $cachedAnnotationReader = null)
  {
      //echo '<b>construct</b>'; exit;
      $this->cachedAnnotationReader = $cachedAnnotationReader;
  }

  public function configure(MappingDriverChain $driverChain)
  {
	 //echo '<b>configure</b>'; exit;

	 if(isset($this->cachedAnnotationReader)){
		$this->usingCachedReader($driverChain);
	 }
  }
  
  private function usingCachedReader(MappingDriverChain $driverChain)
  {
	  // load superclass metadata mapping only, into driver chain
	  // also registers Gedmo annotations.NOTE: you can personalize it
	  \Gedmo\DoctrineExtensions::registerAbstractMappingIntoDriverChainORM(
	    $driverChain,
	    $this->cachedAnnotationReader 
	  );

	  //echo '<b>registerMappingIntoDriverChainORM</b>'; exit;
  }
}