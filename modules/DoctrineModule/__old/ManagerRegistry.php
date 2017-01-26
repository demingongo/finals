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

use Symfony\Component\DependencyInjection\Container;

abstract class ManagerRegistry
{
  protected $api = null;
  protected $doctrine_conn = null;
  private $managers;
  private $connections;
  private $default_connection;
  private $config;
  private $event_manager;
  protected $container;
  
  //private $cache;
  //private $cacheName;

  const NO_MANAGER = 1;
  
  public function __construct(Container $container, array $connections, $default_connection, $config, EventManager $evm, $cacheName = null)// Doctrine\Common\Persistence\Mapping\Driver\MappingDriver $driver , Doctrine\Common\EventManager $evm = null)
  {
	 //dump($evm); exit('- ManagerRegistry -');

	$this->managers = array();
	$this->connections = $connections;
	$this->default_connection = $default_connection;
	$this->config = $config;
	$this->event_manager = $evm;	
	$this->container = $container->get('service_container');


	//$this->cache = null;
	//$this->cacheName = $cacheName;
  }
  

  public function getManager($name = '')
  {
    if (empty($name))
    {
		$name = $this->default_connection;
    }
	
	if (!is_string($name))
    {
      throw new \InvalidArgumentException('Managers::getManager argument must be a string. "'.$name.'" is not.');
    }

    if ( !isset($this->managers[$name]) )
    {
		if( isset($this->connections[$name]) ){
			$this->_ORMConfig($name , $this->connections[$name]);
		}
		else{
			throw new \RuntimeException('Aucune connection ne correspond a l\'argument "'.$name.'"' , self::NO_MANAGER);
		}
	  
    }
    
    return $this->managers[$name];
  }

  public function getConnection($name = '')
  { 
    return $this->getManager($name)->getConnection();
  }

  public function hasManager($name)
  {
	  if (!is_string($name))
	 {
		throw new \InvalidArgumentException('Managers::hasManager argument must be a string. "'.$name.'" is not.');
     }
	  return  isset($this->managers[$name]) || isset($this->connections[$name]);
  }


  private function _ORMConfig($conn_name , Array $params)//$driver , $evm)
  {
	//ORM configuration (http://doctrine-orm.readthedocs.org/en/latest/reference/advanced-configuration.html)

	/*$cache = $this->loadCache();
	//both extends Doctrine\Common\Cache\CacheProvider (MemcacheCache too)
	
	// standard annotation reader
	$annotationReader = new AnnotationReader();
	$cachedAnnotationReader = new CachedReader(
	    $annotationReader, // use reader
	    $cache // and a cache driver
	);*/
	// create a driver chain for metadata reading
	/*$driverChain = new MappingDriverChain();

	$this->registerMappingIntoDriverChainORM($driverChain, $cachedAnnotationReader);

	$pathtoEntityDir = $this->root_dir . '/src/Entity2'; // /src

	$novEntityUser = $this->root_dir . '/lib/myframework/Entity/User'; // /src

	$pathtoProxyDir = $this->root_dir . '/app'; // /src
		
	$modules = array(new \Acme\AcmeModule());
	$entityNamespaces = array();	
		foreach($modules as $module){
			if (!is_dir($dir = $module->getPath().'/Entity')) {
				exit('No entity dir '.$dir.' in '.$module->getName());
			}
			$driver2 = new AnnotationDriver(
					$cachedAnnotationReader,
					array($dir)
			);
			$driverChain->addDriver($driver2, $module->getNamespace().'\Entity');
			$entityNamespaces[$module->getName()] = $module->getNamespace().'\Entity';
		}
	
		$driver = new AnnotationDriver(
			$cachedAnnotationReader,
			array($pathtoEntityDir . '/Entity', $novEntityUser . '/Entity')
		);
		$driverChain->addDriver($driver, 'Entity');*/

	/* instead of building the driver in here , make a class with a method that build it */
	
	//general ORM configuration
	//if ($this->config == null){
	/*$config = new Configuration();
	$config->setQueryCacheImpl($cache);
	$config->setProxyDir($pathtoProxyDir . '/Proxy');
	$config->setProxyNamespace('Proxy');
	$config->setAutoGenerateProxyClasses(!PRODMODE); // in production , set false or Doctrine\Common\Proxy\AbstractProxyFactory::AUTOGENERATE_NEVER after all proxies	were generated (if an Entity has been modified, regenerate Proxies (orm:generate-proxies))
	$config->setMetadataCacheImpl($cache);
	$config->setMetadataDriverImpl($driverChain);

	$entityNamespaces['Entity'] = 'Entity';

	$config->setEntityNamespaces($entityNamespaces);
	$this->config = $config;*/
	//}

	//$this->config->setMetadataDriverImpl($driverChain);

	/* instead of building the EventManager in here , make a class with a method that build it */
	/*$cachedAnnotationReader = null;
	if($this->container->has('managers.cached.annotation_reader')){ $cachedAnnotationReader = $this->container->get('managers.cached.annotation_reader'); }
	$evm = $this->loadEventManager($cachedAnnotationReader);*/

	$evm = clone $this->event_manager;
	//dump($evm); dump($this->event_manager); exit;
	
	// mysql set names UTF-8 if required
	// PDO ignores the charset property before 5.3.6 so the init listener has to be used instead.
	if (isset($params['charset']) && version_compare(PHP_VERSION, '5.3.6', '<')) {
		if ((isset($params['driver']) && stripos($params['driver'], 'mysql') !== false) ||
                 (isset($params['driverClass']) && stripos($params['driverClass'], 'mysql') !== false)) {
			$evm->addEventSubscriber(new \Doctrine\DBAL\Event\Listeners\MysqlSessionInit($params['charset']));
		}
		unset($params['charset']);
	}

	//getting the EntityManager
	return $this->managers[$conn_name] = EntityManager::create(
		$params,
		$this->config,
		$evm
	);
  }

   /**
	* Gets the listeners that are instances of a specific class.
	*
	* @param string $event The name of the class.
	* @return array
	*/
  public function getInstanceListeners($ClassNameInstance)
  {
	 $retour = array();
     $events_listeners = $this->getManager()->getEventManager()->getListeners();
	 foreach ($events_listeners as $event => $listeners) {
		 foreach ($listeners as $listener) {
			$r = new \ReflectionClass($listener);
			if( $listener instanceof $ClassNameInstance && !isset($retour[$r->getName()]) ) {
				$retour[$r->getName()] = $listener;
			}
		 }
	 }
	 return $retour;
  }

  public function getOneInstanceListener($ClassNameInstance)
  {
     $events_listeners = $this->getManager()->getEventManager()->getListeners();
	 foreach ($events_listeners as $event => $listeners) {
		 foreach ($listeners as $listener) {
			$r = new \ReflectionClass($listener);
			if( $ClassNameInstance == $r->getName() ) {
				return $listener;
			}
		 }
	 }
	 return null;
  }

  /*private function loadCache()
  {
	  if($this->cache != null){
		return $this->cache;
		}

	  //if($this->cacheName != null){
		  //if (extension_loaded('apc')) {
            //$cache = new Doctrine\Common\Cache\ApcCache();
		  //}
	  }

	  if(PRODMODE) // globally used cache driver, in production use APC or memcached as Doctrine\Common\Cache\ApcCache or Doctrine\Common\Cache\MemcacheCache
		{
		if (extension_loaded('apc')) {
            $cache = new \Doctrine\Common\Cache\ApcCache();
        }
		else if (extension_loaded('memcached')) {
            $cache = new \Doctrine\Common\Cache\MemcachedCache();
        }
		else if (extension_loaded('memcache')) {
            $cache = new \Doctrine\Common\Cache\MemcacheCache();
        }
		else if (extension_loaded('xcache')) {
            $cache = new \Doctrine\Common\Cache\XcacheCache();
        }
		else {
			$cache = new \Doctrine\Common\Cache\ArrayCache();
		}
	  }
	  else{
		$cache = new \Doctrine\Common\Cache\ArrayCache();
	  }

	  return $this->cache = $cache;
  }

  protected function loadEventManager(CachedReader $cachedAnnotationReader = null )
  {
	if($this->event_manager != null){
		return $this->event_manager;
	}

	// create event manager and hook prefered extension listeners
	$evm = new EventManager();
	
	return $this->event_manager = $evm;
  }*/
}