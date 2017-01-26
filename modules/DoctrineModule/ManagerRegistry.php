<?php
namespace DoctrineModule;

use Doctrine\ORM\Configuration,
    Doctrine\ORM\EntityManager,
	Doctrine\Common\EventManager;

abstract class ManagerRegistry
{
  private $managers;

  private $connections;
  private $default_connection;
  private $config;
  private $event_manager;

  const NO_MANAGER = 1;
  
  public function __construct(array $connections, $default_connection,Configuration $config, EventManager $evm)
  {
	 //dump($evm); exit('- ManagerRegistry -');

	$this->managers = array();
	$this->connections = $connections;
	$this->default_connection = $default_connection;
	$this->config = $config;
	$this->event_manager = $evm;	
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
}