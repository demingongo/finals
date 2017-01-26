<?php
namespace DoctrineModule;

use Doctrine\ORM\Tools\Setup,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Configuration,
	Doctrine\Common\Annotations\AnnotationReader;

use Doctrine\Common\Annotations\CachedReader,
	Doctrine\ORM\Mapping\Driver\AnnotationDriver,
	Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain,
	Doctrine\Common\EventManager;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class Registry extends ManagerRegistry
{
  /*protected function loadEventManager(CachedReader $cachedAnnotationReader = null)
  {
	$evm = parent::loadEventManager();

	if ($cachedAnnotationReader != null){
	// gedmo extension listeners	
	
	// sluggable
	$sluggableListener = new \Gedmo\Sluggable\SluggableListener();
	// you should set the used annotation reader to listener, to avoid creating new one for mapping drivers
	$sluggableListener->setAnnotationReader($cachedAnnotationReader);
	$evm->addEventSubscriber($sluggableListener);
	
	// tree
	$treeListener = new \Gedmo\Tree\TreeListener();
	$treeListener->setAnnotationReader($cachedAnnotationReader);
	$evm->addEventSubscriber($treeListener);
	
	// loggable, not used in example
	//$loggableListener = new Gedmo\Loggable\LoggableListener;
	//$loggableListener->setAnnotationReader($cachedAnnotationReader);
	//$loggableListener->setUsername('admin');
	//$evm->addEventSubscriber($loggableListener);
	
	// timestampable
	$timestampableListener = new \Gedmo\Timestampable\TimestampableListener();
	$timestampableListener->setAnnotationReader($cachedAnnotationReader);
	$evm->addEventSubscriber($timestampableListener);
	
	// blameable	
	$blameableListener = new \Gedmo\Blameable\BlameableListener();
	$blameableListener->setAnnotationReader($cachedAnnotationReader);
	$blameableListener->setUserValue(null); // determine from your environment
	$evm->addEventSubscriber($blameableListener);

	// translatable
	$translatableListener = new \Gedmo\Translatable\TranslatableListener();
	// current translation locale should be set from session or hook later into the listener
	// most important, before entity manager is flushed
	$translatableListener->setTranslatableLocale('en');
	$translatableListener->setDefaultLocale('en');
	$translatableListener->setAnnotationReader($cachedAnnotationReader);
	$evm->addEventSubscriber($translatableListener);

	// uploadable
	$uploadableListener = new \Gedmo\Uploadable\UploadableListener();
	$uploadableListener->setDefaultPath(__DIR__ . '/src');
	$uploadableListener->setAnnotationReader($cachedAnnotationReader);
	$evm->addEventSubscriber($uploadableListener);

	// sortable, not used in example
	//$sortableListener = new Gedmo\Sortable\SortableListener;
	//$sortableListener->setAnnotationReader($cachedAnnotationReader);
	//$evm->addEventSubscriber($sortableListener);
	}

	return $evm;
  }*/
}