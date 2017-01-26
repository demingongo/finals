<?php
namespace DoctrineModule\DependencyInjection;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Parser,
    Symfony\Component\Yaml\Exception\ParseException;

use Symfony\Component\DependencyInjection\Extension\Extension as BaseExtension;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\FileLocator;

class DoctrineExtension extends BaseExtension
{
	private $defaultConnection;
    private $entityManagers;

	/**
     * Used inside metadata driver method to simplify aggregation of data.
     *
     * @var array
     */
    protected $aliasMap = array();

    /**
     * Used inside metadata driver method to simplify aggregation of data.
     *
     * @var array
     */
    protected $drivers = array();

	
	public function getAlias()
    {
        return 'managers';
    }
	
    public function load(array $configs, ContainerBuilder $container)
    {
		//var_dump($configs); exit;
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

		//var_dump($config);exit;

        /*if (!empty($config['dbal'])) {
            $this->dbalLoad($config['dbal'], $container);
        }

        if (!empty($config['orm'])) {
            $this->ormLoad($config['orm'], $container);
        }*/
		if (!$config['activate']) {
			return;
        }

		$loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
		$loader->load('managers.xml');

		if(!isset($config['default_connection'])){
			$config['default_connection'] = array_keys($config['connections'])[0];
		}
		
		$this->defaultConnection = $config['default_connection'];

		/*$container
				->register('managers', '%doctrine.class%')
				->addArgument(new Reference('service_container'))
				->addArgument($config['connections'])
				->addArgument('%managers.default_connection%');*/

		
		
		
		
		/*$container
            ->setDefinition('managers', new DefinitionDecorator('doctrine'))
            ->setArguments(array(
                new Reference('service_container'),
                $config['connections'],
                '%managers.default_connection%',
            ))
        ;*/

		$connections = array();
		$entityManagers = array();
        foreach (array_keys($config['connections']) as $name) {
            $connections[$name] = sprintf('managers.%s_connection', $name);
			$entityManagers[$name] = sprintf('managers.%s_entity_manager', $name);
			
			//$getConnection = new Definition('stdClass', array($name));
			//$getConnection->setFactory(array(new Reference('managers'),'getConnection'));
			
			//$getManager = new Definition('stdClass', array($name));
			//$getManager->setFactory(array(new Reference('managers'),'getManager'));			

			//$container->setDefinition($connections[$name], $getConnection);
			
			//$container->setDefinition($entityManagers[$name], $getManager);

			$container
				->setDefinition($connections[$name], new DefinitionDecorator('doctrine.dbal.connection'))
				->setArguments(array($name))
			;

			$container
				->setDefinition($entityManagers[$name], new DefinitionDecorator('doctrine.orm.entity_manager'))
				->setArguments(array($name))
			;
        }
		//$connections = $config['connections'];
		//var_dump($connections); exit;

        $container->setParameter('managers.connections', $connections);
		$container->setParameter('managers.entity_managers', $entityManagers);
		$container->setParameter('managers.default_connection', $this->defaultConnection);


		$options = array('auto_generate_proxy_classes', 'proxy_dir', 'proxy_namespace');
        foreach ($options as $key) {
            $container->setParameter('managers.'.$key, $config[$key]);
        }
		
		$ormConfigDef = $container->setDefinition('managers.configuration', new DefinitionDecorator('doctrine.orm.configuration'));


		$this->loadOrmEntityManagerMappingInformation($config, $ormConfigDef, $container);		
		$this->loadOrmCacheDrivers($config, $container);



		$methods = array(
            'setMetadataCacheImpl'        => new Reference('managers.metadata_cache'),
            'setQueryCacheImpl'           => new Reference('managers.query_cache'),
            'setResultCacheImpl'          => new Reference('managers.result_cache'),
            'setMetadataDriverImpl'       => new Reference('managers.metadata_driver'),
            'setProxyDir'                 => '%managers.proxy_dir%',
            'setProxyNamespace'           => '%managers.proxy_namespace%',
            'setAutoGenerateProxyClasses' => '%managers.auto_generate_proxy_classes%',
            'setClassMetadataFactoryName' => $config['class_metadata_factory_name'],
            'setDefaultRepositoryClassName' => $config['default_repository_class'],
        );
		// check for version to keep BC
        if (version_compare(\Doctrine\ORM\Version::VERSION, "2.3.0-DEV") >= 0) {
            $methods = array_merge($methods, array(
                'setNamingStrategy'       => new Reference($config['naming_strategy']),
            ));
        }

		foreach ($methods as $method => $arg) {
            $ormConfigDef->addMethodCall($method, array($arg));
        }

		foreach ($config['hydrators'] as $name => $class) {
            $ormConfigDef->addMethodCall('addCustomHydrationMode', array($name, $class));
        }

		if (!empty($config['dql'])) {
            foreach ($config['dql']['string_functions'] as $name => $function) {
                $ormConfigDef->addMethodCall('addCustomStringFunction', array($name, $function));
            }
            foreach ($config['dql']['numeric_functions'] as $name => $function) {
                $ormConfigDef->addMethodCall('addCustomNumericFunction', array($name, $function));
            }
            foreach ($config['dql']['datetime_functions'] as $name => $function) {
                $ormConfigDef->addMethodCall('addCustomDatetimeFunction', array($name, $function));
            }
        }


		/*foreach($container->getParameter('app.modules') as $module){
			if (!is_dir($dir = $module->getPath().'/Entity')) {
				exit('No entity dir '.$dir.' in '.$module->getName());
			}
			$driver2 = new AnnotationDriver(
					$cachedAnnotationReader,
					array($dir)
			);
			$driverChain->addDriver($driver2, $module->getNamespace().'\Entity');
			$entityNamespaces[$module->getName()] = $module->getNamespace().'\Entity';
		}*/


		$container
            ->setDefinition('managers', new DefinitionDecorator('doctrine'))
            ->setArguments(array(
                //new Reference('service_container'),
                $config['connections'],
                '%managers.default_connection%',
			    new Reference('managers.configuration'),
				new Reference('managers.event_manager'),
            ))
        ;

		//var_dump($config);exit;

		/*$enabledFilters = array();
        $filtersParameters = array();
        foreach ($entityManager['filters'] as $name => $filter) {
            $ormConfigDef->addMethodCall('addFilter', array($name, $filter['class']));
            if ($filter['enabled']) {
                $enabledFilters[] = $name;
            }
            if ($filter['parameters']) {
                $filtersParameters[$name] = $filter['parameters'];
            }
        }

        $managerConfiguratorName = sprintf('doctrine.orm.%s_manager_configurator', $entityManager['name']);
        $managerConfiguratorDef = $container
            ->setDefinition($managerConfiguratorName, new DefinitionDecorator('doctrine.orm.manager_configurator.abstract'))
            ->replaceArgument(0, $enabledFilters)
            ->replaceArgument(1, $filtersParameters)
        ;*/

    }

	private function loadConnection()
	{
	}

	protected function getObjectManagerElementName($name)
    {
        return 'managers.'.$name;
    }

	protected function getMappingResourceExtension()
    {
        return 'orm';
    }

	protected function getMappingObjectDefaultName()
    {
        return 'Entity';
    }

	protected function getMappingResourceConfigDirectory()
    {
        return 'config/doctrine';//'Resources/config/doctrine';
    }

	/*
     * @param array            $entityManager A configured ORM entity manager
     * @param Definition       $ormConfigDef  A Definition instance
     * @param ContainerBuilder $container     A ContainerBuilder instance
     */
    protected function loadOrmEntityManagerMappingInformation(array $entityManager, Definition $ormConfigDef, ContainerBuilder $container)
    {
        // reset state of drivers and alias map. They are only used by this methods and children.
        $this->drivers = array();
        $this->aliasMap = array();

        $this->loadMappingInformation($entityManager, $container);
        $this->registerMappingDrivers($entityManager, $container);

        $ormConfigDef->addMethodCall('setEntityNamespaces', array($this->aliasMap));
    }

	/**
     * Loads a configured entity managers cache drivers.
     *
     * @param array            $entityManager A configured ORM entity manager.
     * @param ContainerBuilder $container     A ContainerBuilder instance
     */
    protected function loadOrmCacheDrivers(array $config, ContainerBuilder $container)
    {
        $this->loadObjectManagerCacheDriver($config, $container, 'metadata_cache');
        $this->loadObjectManagerCacheDriver($config, $container, 'result_cache');
        $this->loadObjectManagerCacheDriver($config, $container, 'query_cache');
    }

	private function loadObjectManagerCacheDriver(array $objectManager, ContainerBuilder $container, $cacheName)
	{

		$cacheDriver = $objectManager[$cacheName.'_driver'];
        $cacheDriverService = $this->getObjectManagerElementName($cacheName);

        switch ($cacheDriver['type']) {
            case 'service':
                $container->setAlias($cacheDriverService, new Alias($cacheDriver['id'], false));

                return;
            case 'memcache':
                $memcacheClass = !empty($cacheDriver['class']) ? $cacheDriver['class'] : '%'.$this->getObjectManagerElementName('cache.memcache.class').'%';
                $memcacheInstanceClass = !empty($cacheDriver['instance_class']) ? $cacheDriver['instance_class'] : '%'.$this->getObjectManagerElementName('cache.memcache_instance.class').'%';
                $memcacheHost = !empty($cacheDriver['host']) ? $cacheDriver['host'] : '%'.$this->getObjectManagerElementName('cache.memcache_host').'%';
                $memcachePort = !empty($cacheDriver['port']) || (isset($cacheDriver['port']) && $cacheDriver['port'] === 0)  ? $cacheDriver['port'] : '%'.$this->getObjectManagerElementName('cache.memcache_port').'%';
                $cacheDef = new Definition($memcacheClass);
                $memcacheInstance = new Definition($memcacheInstanceClass);
                $memcacheInstance->addMethodCall('connect', array(
                    $memcacheHost, $memcachePort
                ));
                $container->setDefinition($this->getObjectManagerElementName(/*sprintf('%s_memcache_instance', $objectManager['name'])*/'memcache_instance'), $memcacheInstance);
                $cacheDef->addMethodCall('setMemcache', array(new Reference($this->getObjectManagerElementName(/*sprintf('%s_memcache_instance', $objectManager['name'])*/'memcache_instance'))));
                break;
            case 'memcached':
                $memcachedClass = !empty($cacheDriver['class']) ? $cacheDriver['class'] : '%'.$this->getObjectManagerElementName('cache.memcached.class').'%';
                $memcachedInstanceClass = !empty($cacheDriver['instance_class']) ? $cacheDriver['instance_class'] : '%'.$this->getObjectManagerElementName('cache.memcached_instance.class').'%';
                $memcachedHost = !empty($cacheDriver['host']) ? $cacheDriver['host'] : '%'.$this->getObjectManagerElementName('cache.memcached_host').'%';
                $memcachedPort = !empty($cacheDriver['port']) ? $cacheDriver['port'] : '%'.$this->getObjectManagerElementName('cache.memcached_port').'%';
                $cacheDef = new Definition($memcachedClass);
                $memcachedInstance = new Definition($memcachedInstanceClass);
                $memcachedInstance->addMethodCall('addServer', array(
                    $memcachedHost, $memcachedPort
                ));
                $container->setDefinition($this->getObjectManagerElementName(/*sprintf('%s_memcached_instance', $objectManager['name'])*/'memcached_instance'), $memcachedInstance);
                $cacheDef->addMethodCall('setMemcached', array(new Reference($this->getObjectManagerElementName(/*sprintf('%s_memcached_instance', $objectManager['name'])*/'memcached_instance'))));
                break;
             case 'redis':
                $redisClass = !empty($cacheDriver['class']) ? $cacheDriver['class'] : '%'.$this->getObjectManagerElementName('cache.redis.class').'%';
                $redisInstanceClass = !empty($cacheDriver['instance_class']) ? $cacheDriver['instance_class'] : '%'.$this->getObjectManagerElementName('cache.redis_instance.class').'%';
                $redisHost = !empty($cacheDriver['host']) ? $cacheDriver['host'] : '%'.$this->getObjectManagerElementName('cache.redis_host').'%';
                $redisPort = !empty($cacheDriver['port']) ? $cacheDriver['port'] : '%'.$this->getObjectManagerElementName('cache.redis_port').'%';
                $cacheDef = new Definition($redisClass);
                $redisInstance = new Definition($redisInstanceClass);
                $redisInstance->addMethodCall('connect', array(
                    $redisHost, $redisPort
                ));
                $container->setDefinition($this->getObjectManagerElementName(/*sprintf('%s_redis_instance', $objectManager['name'])*/'redis_instance'), $redisInstance);
                $cacheDef->addMethodCall('setRedis', array(new Reference($this->getObjectManagerElementName(/*sprintf('%s_redis_instance', $objectManager['name'])*/'redis_instance'))));
                break;
            case 'apc':
            case 'array':
            case 'xcache':
            case 'wincache':
            case 'zenddata':
                $cacheDef = new Definition('%'.$this->getObjectManagerElementName(sprintf('cache.%s.class', $cacheDriver['type'])).'%');
                break;
            default:
                throw new \InvalidArgumentException(sprintf('"%s" is an unrecognized Doctrine cache driver.', $cacheDriver['type']));
			}

        $cacheDef->setPublic(false);
        // generate a unique namespace for the given application
        $namespace = 'novice'.$this->getMappingResourceExtension().md5($container->getParameter('app.root_dir').$container->getParameter('app.environment'));
        $cacheDef->addMethodCall('setNamespace', array($namespace));

        $container->setDefinition($cacheDriverService, $cacheDef);
	}

	/**
     * @param array            $objectManager A configured object manager.
     * @param ContainerBuilder $container     A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException
     */
    protected function loadMappingInformation(array $objectManager, ContainerBuilder $container)
    {
        //if ($objectManager['auto_mapping']) {
            // automatically register bundle mappings
            foreach (array_keys($container->getParameter('app.modules')) as $bundle) {
                if (!isset($objectManager['mappings'][$bundle])) {
                    $objectManager['mappings'][$bundle] = array(
                        'mapping'   => true,
                        'is_module' => true,
                    );
                }
            }
        //}

        foreach ($objectManager['mappings'] as $mappingName => $mappingConfig) {
            if (null !== $mappingConfig && false === $mappingConfig['mapping']) {
                continue;
            }

            $mappingConfig = array_replace(array(
                'dir'    => false,
                'type'   => false,
                'prefix' => false,
            ), (array) $mappingConfig);

            $mappingConfig['dir'] = $container->getParameterBag()->resolveValue($mappingConfig['dir']);
            // a bundle configuration is detected by realizing that the specified dir is not absolute and existing

            if (!isset($mappingConfig['is_module'])) {
                $mappingConfig['is_module'] = !is_dir($mappingConfig['dir']);
            }

            if ($mappingConfig['is_module']) {
                $bundle = null;
                foreach ($container->getParameter('app.modules') as $name => $class) {
                    if ($mappingName === $name) {
                        $bundle = new \ReflectionClass($class);

                        break;
                    }
                }

                if (null === $bundle) {
                    throw new \InvalidArgumentException(sprintf('Module "%s" does not exist or it is not enabled.', $mappingName));
                }

                $mappingConfig = $this->getMappingDriverBundleConfigDefaults($mappingConfig, $bundle, $container);
                if (!$mappingConfig) {
                    continue;
                }
            }

            $this->assertValidMappingConfiguration($mappingConfig/*, $objectManager['name']*/);
            $this->setMappingDriverConfig($mappingConfig, $mappingName);
            $this->setMappingDriverAlias($mappingConfig, $mappingName);

			//dump($mappingConfig); exit($mappingName.' - MyDoctrineExtension');
        }
    }

	/**
     * If this is a bundle controlled mapping all the missing information can be autodetected by this method.
     *
     * Returns false when autodetection failed, an array of the completed information otherwise.
     *
     * @param array            $bundleConfig
     * @param \ReflectionClass $bundle
     * @param ContainerBuilder $container    A ContainerBuilder instance
     *
     * @return array|false
     */
    protected function getMappingDriverBundleConfigDefaults(array $bundleConfig, \ReflectionClass $bundle, ContainerBuilder $container)
    {
        $bundleDir = dirname($bundle->getFilename());

        if (!$bundleConfig['type']) {
            $bundleConfig['type'] = $this->detectMetadataDriver($bundleDir, $container);
        }

        if (!$bundleConfig['type']) {
            // skip this bundle, no mapping information was found.
            return false;
        }

        if (!$bundleConfig['dir']) {
            if (in_array($bundleConfig['type'], array('annotation', 'staticphp'))) {
                $bundleConfig['dir'] = $bundleDir.'/'.$this->getMappingObjectDefaultName();
            } else {
                $bundleConfig['dir'] = $bundleDir.'/'.$this->getMappingResourceConfigDirectory();
            }
        } else {
            $bundleConfig['dir'] = $bundleDir.'/'.$bundleConfig['dir'];
        }

        if (!$bundleConfig['prefix']) {
            $bundleConfig['prefix'] = $bundle->getNamespaceName().'\\'.$this->getMappingObjectDefaultName();
        }

        return $bundleConfig;
    }

	/**
     * Detects what metadata driver to use for the supplied directory.
     *
     * @param string           $dir       A directory path
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @return string|null A metadata driver short name, if one can be detected
     */
    protected function detectMetadataDriver($dir, ContainerBuilder $container)
    {
        // add the closest existing directory as a resource
        $configPath = $this->getMappingResourceConfigDirectory();
        $resource = $dir.'/'.$configPath;
        while (!is_dir($resource)) {
            $resource = dirname($resource);
        }

        $container->addResource(new FileResource($resource));

        $extension = $this->getMappingResourceExtension();
        if (($files = glob($dir.'/'.$configPath.'/*.'.$extension.'.xml')) && count($files)) {
            return 'xml';
        } elseif (($files = glob($dir.'/'.$configPath.'/*.'.$extension.'.yml')) && count($files)) {
            return 'yml';
        } elseif (($files = glob($dir.'/'.$configPath.'/*.'.$extension.'.php')) && count($files)) {
            return 'php';
        }

        // add the directory itself as a resource
        $container->addResource(new FileResource($dir));

        if (is_dir($dir.'/'.$this->getMappingObjectDefaultName())) {
            return 'annotation';
        }

        return null;
    }

	/**
     * Assertion if the specified mapping information is valid.
     *
     * @param array  $mappingConfig
     * @param string $objectManagerName
     *
     * @throws \InvalidArgumentException
     */
    protected function assertValidMappingConfiguration(array $mappingConfig/*, $objectManagerName*/)
    {
        if (!$mappingConfig['type'] || !$mappingConfig['dir'] || !$mappingConfig['prefix']) {
            throw new \InvalidArgumentException(/*sprintf('Mapping definitions for Doctrine manager "%s" require at least the "type", "dir" and "prefix" options.', $objectManagerName)*/
			'Mapping definitions for Doctrine manager require at least the "type", "dir" and "prefix" options.');
        }

        if (!is_dir($mappingConfig['dir'])) {
            throw new \InvalidArgumentException(sprintf('Specified non-existing directory "%s" as Doctrine mapping source.', $mappingConfig['dir']));
        }

        if (!in_array($mappingConfig['type'], array('xml', 'yml', 'annotation', 'php', 'staticphp'))) {
            throw new \InvalidArgumentException(/*sprintf('Can only configure "xml", "yml", "annotation", "php" or '.
                '"staticphp" through the DoctrineBundle. Use your own bundle to configure other metadata drivers. '.
                'You can register them by adding a new driver to the '.
                '"%s" service definition.', $this->getObjectManagerElementName($objectManagerName.'.metadata_driver'))*/
				'Can only configure "xml", "yml", "annotation", "php" or '.
                '"staticphp" for mapping type'
            );
        }
    }

	/**
     * Register the mapping driver configuration for later use with the object managers metadata driver chain.
     *
     * @param array  $mappingConfig
     * @param string $mappingName
     *
     * @throws \InvalidArgumentException
     */
    protected function setMappingDriverConfig(array $mappingConfig, $mappingName)
    {
        if (is_dir($mappingConfig['dir'])) {
            $this->drivers[$mappingConfig['type']][$mappingConfig['prefix']] = realpath($mappingConfig['dir']);
        } else {
            throw new \InvalidArgumentException(sprintf('Invalid Doctrine mapping path given. Cannot load Doctrine mapping/module named "%s".', $mappingName));
        }
    }

    /**
     * Register the alias for this mapping driver.
     *
     * Aliases can be used in the Query languages of all the Doctrine object managers to simplify writing tasks.
     *
     * @param array  $mappingConfig
     * @param string $mappingName
     */
    protected function setMappingDriverAlias($mappingConfig, $mappingName)
    {
        if (isset($mappingConfig['alias'])) {
            $this->aliasMap[$mappingConfig['alias']] = $mappingConfig['prefix'];
        } else {
            $this->aliasMap[$mappingName] = $mappingConfig['prefix'];
        }
    }


	/**
     * Register all the collected mapping information with the object manager by registering the appropriate mapping drivers.
     *
     * @param array            $objectManager
     * @param ContainerBuilder $container     A ContainerBuilder instance
     */
    protected function registerMappingDrivers($objectManager, ContainerBuilder $container)
    {
        // configure metadata driver for each bundle based on the type of mapping files found
        if ($container->hasDefinition($this->getObjectManagerElementName(/*$objectManager['name'].'_metadata_driver'*/'metadata_driver'))) {
            $chainDriverDef = $container->getDefinition($this->getObjectManagerElementName(/*$objectManager['name'].'_metadata_driver'*/'metadata_driver'));
        } else {
            $chainDriverDef = new Definition('%'.$this->getObjectManagerElementName('metadata.driver_chain.class%'));
            $chainDriverDef->setPublic(false);
        }

        foreach ($this->drivers as $driverType => $driverPaths) {
            $mappingService = $this->getObjectManagerElementName(/*$objectManager['name'].'_'.*/$driverType.'_metadata_driver');
            if ($container->hasDefinition($mappingService)) {
                $mappingDriverDef = $container->getDefinition($mappingService);
                $args = $mappingDriverDef->getArguments();
                if ($driverType == 'annotation') {
                    $args[1] = array_merge(array_values($driverPaths), $args[1]);
                } else {
                    $args[0] = array_merge(array_values($driverPaths), $args[0]);
                }
                $mappingDriverDef->setArguments($args);
            } elseif ($driverType == 'annotation') {
				if(!$container->hasDefinition('managers.cached.annotation_reader')){
					$cachedReader = new Definition('%'.$this->getObjectManagerElementName('metadata.cached_reader.class%'), array(
						new Reference($this->getObjectManagerElementName('metadata.annotation_reader')),
						new Reference('novice.cache'),
					));

					$container->setDefinition('managers.cached.annotation_reader',$cachedReader);
				}

                $mappingDriverDef = new Definition('%'.$this->getObjectManagerElementName('metadata.'.$driverType.'.class%'), array(
                    new Reference($this->getObjectManagerElementName('cached.annotation_reader')),//('metadata.annotation_reader')),
                    array_values($driverPaths)
                ));
            } else {
                $mappingDriverDef = new Definition('%'.$this->getObjectManagerElementName('metadata.'.$driverType.'.class%'), array(
                    array_values($driverPaths)
                ));
            }
            $mappingDriverDef->setPublic(false);
            if (false !== strpos($mappingDriverDef->getClass(), 'yml') || false !== strpos($mappingDriverDef->getClass(), 'xml')) {
                $mappingDriverDef->setArguments(array(array_flip($driverPaths)));
                $mappingDriverDef->addMethodCall('setGlobalBasename', array('mapping'));
            }

            $container->setDefinition($mappingService, $mappingDriverDef);

            foreach ($driverPaths as $prefix => $driverPath) {
                $chainDriverDef->addMethodCall('addDriver', array(new Reference($mappingService), $prefix));
            }
        }
		
		if($container->hasDefinition('managers.cached.annotation_reader')){
			$container->setDefinition('managers.mapping.driver_configurator', new Definition('%managers.mapping.driver_configurator.class%'))
						->setArguments(array(new Reference('managers.cached.annotation_reader')))
						->setPublic(false);

			$chainDriverDef->setConfigurator(array(new Reference('managers.mapping.driver_configurator'),'configure',));
		}

        $container->setDefinition($this->getObjectManagerElementName(/*$objectManager['name'].'_metadata_driver'*/'metadata_driver'), $chainDriverDef);

		/*$gedmoDoctrineExtension = new Definition('%gedmo.doctrine_extensions.class%', array());
		$container->setDefinition('gedmo.doctrine_extensions',$gedmoDoctrineExtension)
				  ->addMethodCall('registerAbstractMappingIntoDriverChainORM', array(	 
					new Reference($this->getObjectManagerElementName('metadata_driver')),
					new Reference('managers.cached.annotation_reader'),		
					));*/
		/*$container->setDefinition('driver_chain', 
									new Definition('%mapping.doctrine_extensions.class%' , 
													array(	new Reference($this->getObjectManagerElementName('metadata_driver')),
															new Reference('managers.cached.annotation_reader'),
													))
									)
			->setFactoryClass('%newsletter_factory.class%')
			->setFactoryMethod('registerMappingIntoDriverChainORM');*/
    }


	/**
     * Loads the DBAL configuration.
     *
     * Usage example:
     *
     *      <doctrine:dbal id="myconn" dbname="sfweb" user="root" />
     *
     * @param array            $config    An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    protected function dbalLoad(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('mydbal.xml');

		//var_dump($config/*['connections']['default']*/);exit;

        if (empty($config['default_connection'])) {
            $keys = array_keys($config['connections']);
            $config['default_connection'] = reset($keys);
        }
        $this->defaultConnection = $config['default_connection'];

        $container->setAlias('database_connection', sprintf('doctrine.dbal.%s_connection', $this->defaultConnection));
        $container->setAlias('doctrine.dbal.event_manager', new Alias(sprintf('doctrine.dbal.%s_connection.event_manager', $this->defaultConnection), false));

        $container->setParameter('doctrine.dbal.connection_factory.types', $config['types']);

        $connections = array();
        foreach (array_keys($config['connections']) as $name) {
            $connections[$name] = sprintf('doctrine.dbal.%s_connection', $name);
        }
        $container->setParameter('doctrine.connections', $connections);
        $container->setParameter('doctrine.default_connection', $this->defaultConnection);

        foreach ($config['connections'] as $name => $connection) {
            $this->loadDbalConnection($name, $connection, $container);
        }
    }

	/**
     * Loads a configured DBAL connection.
     *
     * @param string           $name       The name of the connection
     * @param array            $connection A dbal connection configuration.
     * @param ContainerBuilder $container  A ContainerBuilder instance
     */
    protected function loadDbalConnection($name, array $connection, ContainerBuilder $container)
    {
        // configuration
        $configuration = $container->setDefinition(sprintf('doctrine.dbal.%s_connection.configuration', $name), new DefinitionDecorator('doctrine.dbal.connection.configuration'));
        $logger = null;
        if ($connection['logging']) {
            $logger = new Reference('doctrine.dbal.logger');
        }
        unset ($connection['logging']);

        /*if ($connection['profiling']) {
            $profilingLoggerId = 'doctrine.dbal.logger.profiling.'.$name;
            $container->setDefinition($profilingLoggerId, new DefinitionDecorator('doctrine.dbal.logger.profiling'));
            $logger = new Reference($profilingLoggerId);
            $container->getDefinition('data_collector.doctrine')->addMethodCall('addLogger', array($name, $logger));

            if (null !== $logger) {
                $chainLogger = new DefinitionDecorator('doctrine.dbal.logger.chain');
                $chainLogger->addMethodCall('addLogger', array($logger));

                $loggerId = 'doctrine.dbal.logger.chain.'.$name;
                $container->setDefinition($loggerId, $chainLogger);
                $logger = new Reference($loggerId);
            }
        }*/
        unset($connection['profiling']);

        if (isset($connection['schema_filter']) && $connection['schema_filter']) {
            $configuration->addMethodCall('setFilterSchemaAssetsExpression', array($connection['schema_filter']));
        }

        unset($connection['schema_filter']);

        if ($logger) {
            $configuration->addMethodCall('setSQLLogger', array($logger));
        }

        // event manager
        $def = $container->setDefinition(sprintf('doctrine.dbal.%s_connection.event_manager', $name), new DefinitionDecorator('doctrine.dbal.connection.event_manager'));

        // connection
        // PDO ignores the charset property before 5.3.6 so the init listener has to be used instead.
        if (isset($connection['charset']) && version_compare(PHP_VERSION, '5.3.6', '<')) {
            if ((isset($connection['driver']) && stripos($connection['driver'], 'mysql') !== false) ||
                 (isset($connection['driver_class']) && stripos($connection['driver_class'], 'mysql') !== false)) {
                $mysqlSessionInit = new Definition('%doctrine.dbal.events.mysql_session_init.class%');
                $mysqlSessionInit->setArguments(array($connection['charset']));
                $mysqlSessionInit->setPublic(false);
                $mysqlSessionInit->addTag('doctrine.event_subscriber', array('connection' => $name));

                $container->setDefinition(
                    sprintf('doctrine.dbal.%s_connection.events.mysqlsessioninit', $name),
                    $mysqlSessionInit
                );
                unset($connection['charset']);
            }
        }

		var_dump($container->getServiceIds());
		var_dump($connection);exit;

        $options = $this->getConnectionOptions($connection);

        $container
            ->setDefinition(sprintf('doctrine.dbal.%s_connection', $name), new DefinitionDecorator('doctrine.dbal.connection'))
            ->setArguments(array(
                $options,
                new Reference(sprintf('doctrine.dbal.%s_connection.configuration', $name)),
                new Reference(sprintf('doctrine.dbal.%s_connection.event_manager', $name)),
                $connection['mapping_types'],
            ))
        ;
    }

	protected function getConnectionOptions($connection)
    {
        $options = $connection;

        if (isset($options['platform_service'])) {
            $options['platform'] = new Reference($options['platform_service']);
            unset($options['platform_service']);
        }
        unset($options['mapping_types']);

        foreach (array(
            'options'       => 'driverOptions',
            'driver_class'  => 'driverClass',
            'wrapper_class' => 'wrapperClass',
            'keep_slave'    => 'keepSlave',
        ) as $old => $new) {
            if (isset($options[$old])) {
                $options[$new] = $options[$old];
                unset($options[$old]);
            }
        }

        if (!empty($options['slaves'])) {
            $nonRewrittenKeys = array(
                'driver' => true, 'driverOptions' => true, 'driverClass' => true,
                'wrapperClass' => true, 'keepSlave' => true,
                'platform' => true, 'slaves' => true, 'master' => true,
                // included by safety but should have been unset already
                'logging' => true, 'profiling' => true, 'mapping_types' => true, 'platform_service' => true,
            );
            foreach ($options as $key => $value) {
                if (isset($nonRewrittenKeys[$key])) {
                    continue;
                }
                $options['master'][$key] = $value;
                unset($options[$key]);
            }
            if (empty($options['wrapperClass'])) {
                // Change the wrapper class only if the user does not already forced using a custom one.
                $options['wrapperClass'] = 'Doctrine\\DBAL\\Connections\\MasterSlaveConnection';
            }
        } else {
            unset($options['slaves']);
        }

        return $options;
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration($container->getParameter('app.debug'));
    }

	/*public function getAlias()
    {
        return 'doctrine';
    }*/
    /*public function load($resource, $type = null)
    {
		// Locate the file
		$resource = basename ($resource);
		$files = $this->getLocator()->locate($resource, null, false);
		
		// ... handle the config values
		
		$processor = new \Symfony\Component\Config\Definition\Processor();
		$yaml = new Parser();
		$configValues = array();
		
		$file = $files[0];
		$cachePath = dirname($file).'/cache/configuration.yml';
		$configMatcherCache = new \Symfony\Component\Config\ConfigCache($cachePath, !PRODMODE);
	if (!$configMatcherCache->isFresh()) {
		//foreach($files as $file) {
			$resources[]=new \Symfony\Component\Config\Resource\FileResource($file);
			try {
				$configValues = array_merge($configValues,$yaml->parse(file_get_contents($file)));
				// if there is a 'imports' section, check it
				if(isset($configValues['imports'])) {
					$configuration = new \Novice\Config\Definition\ImportsConfig(PRODMODE);
					$array = array('imports'=>$configValues['imports']);
					$configValues['imports'] = $processor->processConfiguration($configuration, $array); //($tree, $content)
					if(!empty($configValues['imports'])) {
						$arrayImports = $configValues['imports'];
						foreach($arrayImports as $arrayResource){
							foreach($arrayResource as $resource){
								//echo dirname($file)."/".$resource; exit;
								$resources[]=new \Symfony\Component\Config\Resource\FileResource(dirname($file)."/".$resource);
								$configValues = array_merge($yaml->parse(file_get_contents(dirname($file)."/".$resource)) , $configValues);
							}
						}
					}
					unset($configValues['imports']);
				}
			}
			catch (ParseException $e) {
				throw  new \RuntimeException("Unable to parse the YAML string: %s", $e->getMessage());
			}
		//}
		
		// if there is a 'doctrine' section, check it
		if(isset($configValues['doctrine'])) {
			$configuration = new \Novice\Config\Definition\DoctrineConfig(PRODMODE);
			$array = array('doctrine'=>$configValues['doctrine']);
			$configValues['doctrine'] = $processor->processConfiguration($configuration, $array); //($tree, $content)
		}

		// maybe import some other resource:

        // $this->import('extra_users.yml');

		//return $configValues;

		$code = \Symfony\Component\Yaml\Yaml::dump($configValues, 1); //2, 3 ou plus pour que ca soit human-readable

		$configMatcherCache->write($code, $resources);
	}

	  return \Symfony\Component\Yaml\Yaml::parse(file_get_contents($cachePath));

    }

    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'yml' === pathinfo(
            $resource,
            PATHINFO_EXTENSION
        ) && (strpos(basename($resource),'db_') === 0); //if file is yml and name starts with 'config'
    }*/
}