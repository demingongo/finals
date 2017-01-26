<?php
namespace Novice\DependencyInjection;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Parser,
    Symfony\Component\Yaml\Exception\ParseException;

use Symfony\Component\DependencyInjection\Extension\Extension as BaseExtension;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder,
	Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\Finder\Finder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\FileLocator;

use Novice\Events;

class FrameworkExtension extends BaseExtension
{
	protected $events;

	public function getAlias()
    {
        return 'framework';
    }

	protected function getObjectElementName($name)
    {
        return 'framework.'.$name;
    }

	public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration($container->getParameter('app.environment'), $container->getParameter('app.debug'));
    }

	public function load(array $configs, ContainerBuilder $container)
    {
		$configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

		/*$method = array_search('app.request', $this->getFrameworkEvents(), true);
		dump('on'.ucfirst(strtolower($method)));	*/	
		//dump($config); exit('-FrameworkExtension-');
		
		$appDefinition = new Definition();
		$appDefinition->setSynthetic(true);
		$container->setDefinition('app', $appDefinition);


		$loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
		$loader->load('framework.xml');
		$loader->load('translation.xml');
		
		$this->registerAnnotationConfiguration(/*$config['annotation'],*/ $container);

		$this->registerRouterConfiguration($config['router'], $container);

		$this->registerAssetConfiguration($config['asset'], $container);

		$this->registerTemplatingConfiguration($config['templating'], $container);

		//$this->registerMiddlewaresConfiguration($config['middlewares'], $container);

		$this->registerSessionConfiguration($container);

		$this->registerMiddlewareDispatcherConfiguration($config['listeners'], $container);
		
		$this->registerNoviceMiddlewares($container);

		$this->registerHTMLPurifierConfiguration($container);

		//dump($config['translator']);
		//exit(__METHOD__);

		$this->registerTranslatorConfiguration($config['translator'], $container);

		//dump($container->getParameterBag()->all());
		
	}

	/**
     * Loads the translator configuration.
     *
     * @param array            $config    A translator configuration array
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    private function registerTranslatorConfiguration(array $config, ContainerBuilder $container)
    {
        if (!$this->isConfigEnabled($container, $config)) {
            return;
        }

        // Use the "real" translator instead of the identity default
        $container->setAlias('translator', 'translator.default');
        $translator = $container->findDefinition('translator.default');
        if (!is_array($config['fallbacks'])) {
            $config['fallbacks'] = array($config['fallbacks']);
        }

        $translator->addMethodCall('setFallbackLocales', array($config['fallbacks']));

        // Discover translation directories
        $dirs = array();
        /*if (class_exists('Symfony\Component\Validator\Validator')) {
            $r = new \ReflectionClass('Symfony\Component\Validator\Validator');

            $dirs[] = dirname($r->getFilename()).'/Resources/translations';
        }
        if (class_exists('Symfony\Component\Form\Form')) {
            $r = new \ReflectionClass('Symfony\Component\Form\Form');

            $dirs[] = dirname($r->getFilename()).'/Resources/translations';
        }*/
        if (class_exists('Symfony\Component\Security\Core\Exception\AuthenticationException')) {
            $r = new \ReflectionClass('Symfony\Component\Security\Core\Exception\AuthenticationException');

            $dirs[] = dirname($r->getFilename()).'/../Resources/translations';
        }
        $overridePath = $container->getParameter('app.root_dir').'/Resources/%s/translations';
        foreach ($container->getParameter('app.modules') as $module => $class) {
            $reflection = new \ReflectionClass($class);
            if (is_dir($dir = dirname($reflection->getFilename()).'/Resources/translations')) {
                $dirs[] = $dir;
            }
            if (is_dir($dir = sprintf($overridePath, $module))) {
                $dirs[] = $dir;
            }
        }
        if (is_dir($dir = $container->getParameter('app.root_dir').'/Resources/translations')) {
            $dirs[] = $dir;
        }
		/*dump($dirs);
		exit();*/

        // Register translation resources
        if ($dirs) {
            foreach ($dirs as $dir) {
                $container->addResource(new DirectoryResource($dir));
            }
            $finder = Finder::create()
                ->files()
                ->filter(function (\SplFileInfo $file) {
                    return 2 === substr_count($file->getBasename(), '.') && preg_match('/\.\w+$/', $file->getBasename());
                })
                ->in($dirs)
            ;

            foreach ($finder as $file) {
                // filename is domain.locale.format
                list($domain, $locale, $format) = explode('.', $file->getBasename(), 3);
                $translator->addMethodCall('addResource', array($format, (string) $file, $locale, $domain));
            }
        }
    }
	
	private function registerAnnotationConfiguration(/*array $annotation,*/ ContainerBuilder $container)
    {
		$cache_dir = $container->getParameter('app.cache_dir').'/annotations';
		
		/*$annReader = new Definition('Doctrine\Common\Annotations\AnnotationReader');
		$annReader->setPublic(false);
		$container->setDefinition('doctrine.annotation.reader',$annReader);*/
		
		$annReader = new Reference('annotation_reader');
		
		$fileCache = new Definition('Doctrine\Common\Cache\FilesystemCache');
		$fileCache->setArguments(array($cache_dir, ".data", 0002));
		$fileCache->setPublic(false);
		$container->setDefinition('framework.cache.annotation_file_cache',$fileCache);
		
		$fileCache = new Reference('framework.cache.annotation_file_cache');
		
		$cachedReader = new Definition('Doctrine\Common\Annotations\CachedReader');
		$cachedReader->setArguments(array($annReader, $fileCache,$container->getParameter('app.debug')));
		$cachedReader->setPublic(false);
		$container->setDefinition('framework.cached.annotation_reader',$cachedReader);
	}

	private function registerRouterConfiguration(array $router, ContainerBuilder $container)
    {
		$this->loadRouterLoaderResolver($container);
		
		$container
			->setDefinition('router.loader', new DefinitionDecorator('router.loader.abstract'))
			->setArguments(array(new Reference('router.loader_resolver')))
		;

		/*$paramRouter = array(	'cache_dir' => '%app.cache_dir%'.'/symfony/routing',
								'debug' => '%app.debug%',
										);*/
		
		$baseUrl = '';//getenv('REDIRECT_REDIRECT_BASE') ? getenv('REDIRECT_REDIRECT_BASE') : getenv('BASE');

		//dump($baseUrl); exit;

		$requestContext = new Definition('%router.request_context.class%');
        $requestContext->setArguments(array($baseUrl));
        //$requestContext->setPublic(false);

        $container->setDefinition(
			'router.request_context',
			$requestContext
		);

		foreach ($router['options'] as $key => $val) {
			$container->setParameter('router.'.$key , $val);
		}

		/*dump($router['options']);
		exit();*/

		$args = array(
			new Reference('service_container'),
			new Reference('router.loader'),
			$router['resource'],
			$router['options'],
			new Reference('router.request_context'),
							);

		$container
				->setDefinition('router', new DefinitionDecorator('framework.router'))
				->setArguments($args)
			;
	}


	private function loadRouterLoaderResolver(ContainerBuilder $container)
    {
		$locator = new Definition('%framework.file_locator.class%'); //Symfony\Component\Config\FileLocator
		$locator->setArguments(array(new Reference('app'),'%app.root_dir%'.'/config'));
		$locator->setPublic(false);
		$container->setDefinition('router.file_locator',$locator);

		$filelocator = new Reference('router.file_locator');
		$servicecontainer = new Reference('service_container');

		$yaml = new Definition('Novice\Routing\Loader\YamlFileLoader');
		$yaml->setArguments(array($servicecontainer, $filelocator));
		$yaml->setPublic(false);

		$xml = new Definition('Novice\Routing\Loader\XmlFileLoader');
		$xml->setArguments(array($servicecontainer, $filelocator));
		$xml->setPublic(false);

		$php = new Definition('Symfony\Component\Routing\Loader\PhpFileLoader');
		$php->setArguments(array($filelocator));
		$php->setPublic(false);
		
		$closure = new Definition('Symfony\Component\Routing\Loader\ClosureLoader');
		$closure->setArguments(array($filelocator));
		$closure->setPublic(false);
		
		/*** pour annotations **/
		$annReader = new Reference('framework.cached.annotation_reader');
		
		
		$annCL = new Definition('Novice\Routing\Annotation\AnnotationClassLoader');
		$annCL->setArguments(array($annReader));
		$annCL->setPublic(false);
		$container->setDefinition('annotation.loader',$annCL);
		
		$annCL = new Reference('annotation.loader');
		
		$annFile = new Definition('Symfony\Component\Routing\Loader\AnnotationFileLoader');
		$annFile->setArguments(array($filelocator, $annCL));
		$annFile->setPublic(false);
		
		$annDir = new Definition('Symfony\Component\Routing\Loader\AnnotationDirectoryLoader');
		$annDir->setArguments(array($filelocator, $annCL));
		$annDir->setPublic(false);
		
		
		$container->setDefinition('router.loader.annotation.file',$annFile);
		$container->setDefinition('router.loader.annotation.directory',$annDir);
		
		$container->setDefinition('router.loader.closure',$closure);
		$container->setDefinition('router.loader.php',$php);		
		$container->setDefinition('router.loader.xml',$xml);
		$container->setDefinition('router.loader.yaml',$yaml);

		$loaderResolver = new Definition('Symfony\Component\Config\Loader\LoaderResolver');
		$loaderResolver->setArguments(array(array(
			new Reference('router.loader.yaml'),
			new Reference('router.loader.xml'),
			new Reference('router.loader.php'),
			new Reference('router.loader.closure'),
			new Reference('router.loader.annotation.file'),
			new Reference('router.loader.annotation.directory'),
			//new AnnotationFileLoader($locator , AnnotationClassLoader $loader),
			//new AnnotationDirectoryLoader($locator, AnnotationClassLoader $loader),
												  )
								)
							);
		$loaderResolver->setPublic(false);
		$container->setDefinition('router.loader_resolver',$loaderResolver);
	}


	private function registerAssetConfiguration(array $asset,ContainerBuilder $container)
    {
		$this->registerAssetVersionStrategy('asset.packages.version_strategy', $asset, $container);

		$versionStrategy = new Reference('asset.packages.version_strategy');

		if(empty($asset['packages']['urls']))
			$asset['packages']['urls'] = array();

		if(empty($asset['packages']['paths']))
			$asset['packages']['paths'] = array();
		

			$packageFactory = new Definition('Novice\Templating\Asset\PackageFactory');
			$packageFactory->setArguments(array(
				new Reference('service_container'),
				$versionStrategy,
				$asset['default_path'],
				$asset['packages']['paths'],
				$asset['packages']['urls'],
				)
			);
			$packageFactory->addMethodCall('createPackages', array());
			$container->setDefinition('asset.package_factory', $packageFactory)
				      ->setPublic(false);
				      //->setScope('request');
	}


	private function registerAssetVersionStrategy($name, array $asset,ContainerBuilder $container)
    {
		$versionStrategy;

		if(isset($asset['strategy'])){
			if(!empty($asset['strategy']['service'])){
				if($name != $asset['strategy']['service']){
					$container->setAlias($name, $asset['strategy']['service']);
				}
				return;
			}
			else if(!empty($asset['strategy']['version'])){
				$container->setParameter('asset.version_strategy.version', $asset['strategy']['version']);
				$container->setParameter('asset.version_strategy.format', $asset['strategy']['format']);

				$versionStrategy = new Definition('Symfony\Component\Asset\VersionStrategy\StaticVersionStrategy');
				$versionStrategy->setArguments(array('%asset.version_strategy.version%', '%asset.version_strategy.format%'));
			}
		}

		if(!is_object($versionStrategy)){
			$versionStrategy = new Definition('Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy');
		}

		$container->setDefinition($name, $versionStrategy);
	}


	private function registerTemplatingConfiguration(array $templating,ContainerBuilder $container)
    {
		$rdir = $container->getParameter('app.root_dir');
		$cdir = $container->getParameter('app.cache_dir');
		$resources_dir = '/Resources';
		$compile_dir = '/smarty/template_c/';
		$cache_dir = '/smarty/cache/';
		$template_dir = '/views/';
		$error_dir = '/views/errors/';
		$config_dir = '/config/';
		$plugins_dir = '/plugins/';
		$noviceModule = new \ReflectionClass($container->getParameter('app.modules')['FrameworkModule']);
		$noviceModuleDir = dirname($noviceModule->getFilename());

		$templatingService = new DefinitionDecorator('framework.templating');
		$templatingService
			->addMethodCall('setCompileDir', array($cdir.$compile_dir))
			->addMethodCall('setCacheDir', array($cdir.$cache_dir))
			->addMethodCall('setTemplateDir', array($rdir.$resources_dir.$template_dir))
			->addMethodCall('addTemplateDir', array($rdir.$resources_dir.$error_dir, 'errors'))
			->addMethodCall('setConfigDir', array($rdir.$resources_dir.$config_dir))
			->addMethodCall('addPluginsDir', array($noviceModuleDir.$resources_dir.$plugins_dir))
			->addMethodCall('addPluginsDir', array($rdir.$resources_dir.$plugins_dir))
				  ->addMethodCall('setDebugging', array($templating['debugging']))
			      ->addMethodCall('setCacheLifetime', array($templating['cache_lifetime']))
			      ->addMethodCall('setLeftDelimiter', array($templating['left_delimiter']))
			      ->addMethodCall('setRightDelimiter', array($templating['right_delimiter']));

		foreach($container->getParameter('app.modules') as $name => $class) {
			$module = new \ReflectionClass($class);
			$moduleDir = dirname($module->getFilename());

			if (is_dir($dir = $moduleDir.$resources_dir.$template_dir)) {
                $templatingService->addMethodCall('addTemplateDir', array($dir, $name));
            }
			if (is_dir($dir = $moduleDir.$resources_dir.$config_dir)) {
                $templatingService->addMethodCall('addConfigDir', array($dir, $name));
            }				  
			if (is_dir($dir = $moduleDir.$resources_dir.$plugins_dir)) {
                $templatingService->addMethodCall('addPluginsDir', array($dir, $name));
            }
        }

		if(in_array($container->getParameter('app.environment'), array('dev', 'test'))){
			$templatingService->addMethodCall('setCompileCheck',array(\Smarty::COMPILECHECK_CACHEMISS));			
		}else{
			$templatingService->addMethodCall('setCompileCheck',array(\Smarty::COMPILECHECK_OFF));
		}

		$templatingService->addMethodCall('setAssets', array( new Reference('asset.package_factory', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, false) ));

		$container->setDefinition('templating', $templatingService);
	}


	private function registerMiddlewaresConfiguration(array $middlewares, ContainerBuilder $container)
    {
		/*un service middlewares qui est une classe 'registry' contenant les middlewares et leurs carateristiques
		  dans des attributs ,
		  et une methode 'handle(HTTPRequest $request, HTTPResponse $reponse)' qui execute les middlewares necessaire
		*/

		/*$registry = new Definition('%middlewares.class%');
        $registry->setArguments(array(new Reference('service_container'), $middlewares));

        $container->setDefinition(
			'middlewares',
			$registry
		);*/
	}

	private function registerMiddlewareDispatcherConfiguration(array $middlewares, ContainerBuilder $container)
	{
		$dispatcher = new Definition('%event_dispatcher.class%');
		$dispatcher->setArguments(array(new Reference('service_container'), ));

		
		//-BEGIN- how to register a middleware
		foreach($middlewares as $name => $params){
			$container->setParameter('framework.middleware.'.$name.'.class', $params['class']);
			$container->setDefinition(
				'framework.middleware.'.$name,
				new Definition('%framework.middleware.'.$name.'.class%')
			);
			foreach($params['events'] as $event){ 
				$args = array();
				$args[] = $event;
				
				$method = strtolower(array_search($event, $this->getFrameworkEvents(), true));

				$container->setParameter('framework.middleware_'.$method.'.'.$name.'.priority', $params['priority']);
				$container->setParameter('framework.middleware_'.$method.'.'.$name.'.pattern', $params['pattern']);

				$args[] = array('framework.middleware.'.$name, 'on'.ucfirst($method));
				$args[] = '%framework.middleware_'.$method.'.'.$name.'.priority%';
				$args[] = '%framework.middleware_'.$method.'.'.$name.'.pattern%';
				//dump($args); exit;
				$dispatcher->addMethodCall('addListenerService',$args);
			}
		}
		//-END- how to register a middleware
		
		$container->setDefinition(
			'event_dispatcher',
			$dispatcher
		)
		->setPublic(false);

		$httpApp = new Definition('Novice\HttpApp');
		$httpApp->setArguments(array(new Reference('event_dispatcher'), ));
		$container->setDefinition(
			'http_app',
			$httpApp
		);
	}

	protected function getFrameworkEvents()
	{
		if(empty($this->events)){
			$refClass = new \ReflectionClass('Novice\Events');
			$this->events = $refClass->getConstants();
		}

		return $this->events;
	}

	private function registerSessionConfiguration(ContainerBuilder $container)
	{
		$container->setParameter('session.storage.metadata_bag.class', 'Symfony\Component\HttpFoundation\Session\Storage\MetadataBag');
		$container->setDefinition(
			'session.storage.metadata_bag',
			new Definition('%session.storage.metadata_bag.class%')
		)
		->setArguments(array('_novice_sf2_meta', 0));

		$container->setParameter('session.storage.class', 'Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage');
		$container->setDefinition(
			'session.storage',
			new Definition('%session.storage.class%')
		)
		->setArguments(array(array(), null, new Reference('session.storage.metadata_bag')));

		$container->setParameter('session.attribute_bag.class', 'Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag');
		$container->setDefinition(
			'session.attribute_bag',
			new Definition('%session.attribute_bag.class%')
		)
		->setArguments(array('_novice_sf2_attributes'));

		$container->setParameter('session.flash_bag.class', 'Symfony\Component\HttpFoundation\Session\Flash\FlashBag');
		$container->setDefinition(
			'session.flash_bag',
			new Definition('%session.flash_bag.class%')
		)
		->setArguments(array('_novice_sf2_flashes'));

        $container->setDefinition(
			'session',
			new Definition('Novice\Session\Session')
		)
		->setArguments(array(
			new Reference('session.storage'),
			new Reference('session.attribute_bag'),
			new Reference('session.flash_bag'),
			))
		->addMethodCall('start');
	}

	private function registerHTMLPurifierConfiguration(ContainerBuilder $container){

		
		$allowed = array(
  'img[src|alt|title|width|height|style|data-mce-src|data-mce-json]',
  'figure', 'figcaption',
  'video[src|type|width|height|poster|preload|controls]', 'source[src|type]',
  'a[href|target]',
  'iframe[width|height|src|frameborder|allowfullscreen]',
  'strong', 'b', 'i', 'u', 'em', 'br', 'font',
  'h1[style]', 'h2[style]', 'h3[style]', 'h4[style]', 'h5[style]', 'h6[style]',
  'p[style]', 'div[style]', 'center', 'address[style]',
  'span[style]', 'pre[style]',
  'ul', 'ol', 'li',
  'table[width|height|border|style]', 'th[width|height|border|style]',
  'tr[width|height|border|style]', 'td[width|height|border|style]',
  'hr'
   );

		$cachedir = $container->getParameter('app.cache_dir').'/htmlpurifier';
		if (!file_exists($cachedir)) {
			mkdir($cachedir);
		}

		$container->setDefinition(
			'html.purifier.config_schema',
			new Definition('HTMLPurifier_HTMLDefinition')
		)
		->setFactory('HTMLPurifier_ConfigSchema::instance');


		$container->setDefinition(
			'html.purifier.config_configurator',
			new Definition('Novice\HTMLPurifier\HTMLPurifier_ConfigConfigurator')
		);

		$container->setDefinition(
			'html.purifier.config',
			new Definition('HTMLPurifier_Config')
		)
		->setArguments(array(new Reference('html.purifier.config_schema')))
		->addMethodCall('set', array('HTML.Doctype', 'HTML 4.01 Transitional'))
		->addMethodCall('set', array('CSS.AllowTricky', true))
		->addMethodCall('set', array('Cache.SerializerPath', '%app.cache_dir%'.'/htmlpurifier'))
		
		// Allow iframes from:
		// o YouTube.com
		// o Vimeo.com
		->addMethodCall('set', array('HTML.SafeIframe', true))
		->addMethodCall('set', array('URI.SafeIframeRegexp', '%%^(http:|https:)?//(www.youtube(?:-nocookie)?.com/embed/|player.vimeo.com/video/)%%'))
		
		// Autorisation des cibles by Demingongo-Litemo Stephane
		->addMethodCall('set', array('Attr.AllowedFrameTargets', array('_blank','_self','#')))
		->addMethodCall('set', array('HTML.Allowed', implode(',', $allowed)))
		
		// Set some HTML5 properties
		->addMethodCall('set', array('HTML.DefinitionID', 'html5-definitions')) // unqiue id
		->addMethodCall('set', array('HTML.DefinitionRev', 1))
		->setConfigurator(array(new Reference('html.purifier.config_configurator'),'configure',));


		$container->setDefinition(
			'html.purifier',
			new Definition('HTMLPurifier')
		)
		->setArguments(array(new Reference('html.purifier.config')));

		$definition = new Definition('Novice\HTMLPurifier\PurifyExtension', array(new Reference('html.purifier')) );
		$definition->addTag('templating.extension',  array('cacheable' => true, 'cache_attrs' => "yay|yay"));
		$definition->setPublic(false);
		$container->setDefinition('html.purifier.templating.purify', $definition);
	}
	
	public function registerNoviceMiddlewares($container){
		
		/*$container->setParameter('novice.template_middleware.class', 'Novice\Middleware\Util\TemplateMiddleware');
		$container->setDefinition(
			'novice.template_middleware',
			new Definition('%novice.template_middleware.class%')
		)
		->setArguments(array(new Reference('service_container')))
		->addTag('app.event_subscriber');*/
		
		
	}
}