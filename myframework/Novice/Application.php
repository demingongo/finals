<?php
namespace Novice;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Loader\IniFileLoader;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\ClosureLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;

use Symfony\Bridge\ProxyManager\LazyProxy\Instantiator\RuntimeInstantiator;
use Symfony\Bridge\ProxyManager\LazyProxy\PhpDumper\ProxyDumper;
use Symfony\Component\HttpKernel\DependencyInjection\MergeExtensionConfigurationPass;
use Novice\DependencyInjection\AddClassesToCachePass;

abstract class Application
{
  /*
  protected $name;
  protected $user;
  protected $config;
  protected $router;*/

	protected $httpRequest;
	protected $httpResponse;

	protected $name;
	protected $modules;
    protected $moduleMap;
    protected $container;
    protected $rootDir;
	protected $environment;
    protected $debug;
    protected $booted;
    protected $startTime;
    //protected $loadClassCache;
	protected $cacheClass;
  
  public function __construct($environment, $debug)
  {
		$this->environment = $environment;
        $this->debug = (Boolean) $debug;
        $this->booted = false;
        $this->rootDir = $this->getRootDir();
        $this->name = $this->getName();
        $this->modules = array();

        if ($this->debug) {
            $this->startTime = microtime(true);
        }
  }

	public function getRootDir()
    {
        if (null === $this->rootDir) {
            $r = new \ReflectionObject($this);
            $this->rootDir = str_replace('\\', '/', dirname($r->getFileName()));
        }

        return $this->rootDir;
    }

	public function getName()
    {
        if (null === $this->name) {
            $this->name = preg_replace('/[^a-zA-Z0-9_]+/', '', basename($this->rootDir));
        }

        return $this->name;
    }

	public function getStartTime()
    {
        return $this->debug ? $this->startTime : null;
    }

	public function getExecutionTime()
    {
        return $this->debug ? -($this->startTime) + microtime(true) : null;
    }

	public function getEnvironment()
    {
        return $this->environment;
    }

	public function isDebug()
    {
        return $this->debug;
    }

	public function getContainer()
    {
        return $this->container;
    }

	public function getModules()
    {
        return $this->modules;
    }

	public function getModule($name, $first = true)
    {
        if (!isset($this->moduleMap[$name])) {
            throw new \InvalidArgumentException(sprintf('Module "%s" does not exist or it is not enabled. Maybe you forgot to add it in the registerModules() method of your %s.php file?', $name, get_class($this)));
        }

        if (true === $first) {
            return $this->moduleMap[$name][0];
        }

        return $this->moduleMap[$name];
    }


	/**
     * Returns the file path for a given resource.
     *
     * A Resource can be a file or a directory.
     *
     * The resource name must follow the following pattern:
     *
     *     @<ModuleName>/path/to/a/file.something
     *
     * where ModuleName is the name of the module
     * and the remaining part is the relative path in the module.
     *
     * If $dir is passed, and the first segment of the path is "Resources",
     * this method will look for a file named:
     *
     *     $dir/<ModuleName>/path/without/Resources
     *
     * before looking in the module resource folder.
     *
     * @param string  $name  A resource name to locate
     * @param string  $dir   A directory where to look for the resource first
     * @param Boolean $first Whether to return the first path or paths for all matching modules
     *
     * @return string|array The absolute path of the resource or an array if $first is false
     *
     * @throws \InvalidArgumentException if the file cannot be found or the name is not valid
     * @throws \RuntimeException         if the name contains invalid/unsafe
     * @throws \RuntimeException         if a custom resource is hidden by a resource in a derived bundle
     *
     * @api
     */
    public function locateResource($name, $dir = null, $first = true)
    {
        if ('@' !== $name[0]) {
            throw new \InvalidArgumentException(sprintf('A resource name must start with @ ("%s" given).', $name));
        }

        if (false !== strpos($name, '..')) {
            throw new \RuntimeException(sprintf('File name "%s" contains invalid characters (..).', $name));
        }

        $bundleName = substr($name, 1);
        $path = '';
        if (false !== strpos($bundleName, '/')) {
            list($bundleName, $path) = explode('/', $bundleName, 2);
        }

        $isResource = 0 === strpos($path, 'Resources') && null !== $dir;
        $overridePath = substr($path, 9);
        $resourceBundle = null;
        $bundles = $this->getModule($bundleName, false);
        $files = array();

        foreach ($bundles as $bundle) {
            if ($isResource && file_exists($file = $dir.'/'.$bundle->getName().$overridePath)) {
                if (null !== $resourceBundle) {
                    throw new \RuntimeException(sprintf('"%s" resource is hidden by a resource from the "%s" derived module. Create a "%s" file to override the module resource.',
                        $file,
                        $resourceBundle,
                        $dir.'/'.$bundles[0]->getName().$overridePath
                    ));
                }

                if ($first) {
                    return $file;
                }
                $files[] = $file;
            }

            if (file_exists($file = $bundle->getPath().'/'.$path)) {
                if ($first && !$isResource) {
                    return $file;
                }
                $files[] = $file;
                $resourceBundle = $bundle->getName();
            }
        }

        if (count($files) > 0) {
            return $first && $isResource ? $files[0] : $files;
        }

        throw new \InvalidArgumentException(sprintf('Unable to find file "%s".', $name));
    }


	public function boot()
    {
        if (true === $this->booted) {
            return;
        }

        /*if ($this->loadClassCache) {
            $this->doLoadClassCache($this->loadClassCache[0], $this->loadClassCache[1]);
        }*/

        // init modules
        $this->initializeModules();

        // init container
        $this->initializeContainer();

        foreach ($this->getModules() as $module) {
            $module->setContainer($this->container);
            $module->boot();
        }

        $this->booted = true;
    }

	public function shutdown()
    {
        if (false === $this->booted) {
            return;
        }

        $this->booted = false;

        foreach ($this->getModules() as $module) {
            $module->shutdown();
            $module->setContainer(null);
        }

        $this->container = null;
    }

  /*public function isProd()
  {   
    return $this->_prod_mode ;
  }
  
  public function getBaseUrl()
  {   
    return $this->httpRequest->getBaseUrl() ;
  }
  
  public function config()
  {   
    return $this->config ;
  }
  
  public function user()
  {   
    return $this->user ;
  }*/


	protected function initializeModules()
    {
        // init modules
        $this->modules = array();
        $topMostModules = array();
        $directChildren = array();

        foreach ($this->registerModules() as $module) {
            $name = $module->getName();
            if (isset($this->modules[$name])) {
                throw new \LogicException(sprintf('Trying to register two modules with the same name "%s"', $name));
            }
            $this->modules[$name] = $module;

            if ($parentName = $module->getParent()) {
                if (isset($directChildren[$parentName])) {
                    throw new \LogicException(sprintf('Module "%s" is directly extended by two modules "%s" and "%s".', $parentName, $name, $directChildren[$parentName]));
                }
                if ($parentName == $name) {
                    throw new \LogicException(sprintf('Module "%s" can not extend itself.', $name));
                }
                $directChildren[$parentName] = $name;
            } else {
                $topMostModules[$name] = $module;
            }
        }

        // look for orphans
        if (count($diff = array_values(array_diff(array_keys($directChildren), array_keys($this->modules))))) {
            throw new \LogicException(sprintf('Module "%s" extends module "%s", which is not registered.', $directChildren[$diff[0]], $diff[0]));
        }

        // inheritance
        $this->moduleMap = array();
        foreach ($topMostModules as $name => $module) {
            $moduleMap = array($module);
            $hierarchy = array($name);

            while (isset($directChildren[$name])) {
                $name = $directChildren[$name];
                array_unshift($moduleMap, $this->modules[$name]);
                $hierarchy[] = $name;
            }

            foreach ($hierarchy as $module) {
                $this->moduleMap[$module] = $moduleMap;
                array_pop($moduleMap);
            }
        }

    }

    /**
     * Gets the container class.
     *
     * @return string The container class
     */
    protected function getContainerClass()
    {
        return $this->name.ucfirst($this->environment).($this->debug ? 'Debug' : '').'ContainerCache';
    }

    /**
     * Gets the container's base class.
     *
     * All names except Container must be fully qualified.
     *
     * @return string
     */
    protected function getContainerBaseClass()
    {
        return 'Container';
    }

    /**
     * Initializes the service container.
     *
     * The cached version of the service container is used when fresh, otherwise the
     * container is built.
     */
    protected function initializeContainer()
    {
        $class = $this->getContainerClass();
        $cache = new ConfigCache($this->getCacheDir().'/symfony/'.$class.'.php', $this->debug);
        $fresh = true;
        if (!$cache->isFresh()) {
            $container = $this->buildContainer();
			$container->register('novice.cache', $this->getCacheClass());
			$container->compile();
            $this->dumpContainer($cache, $container, $class, $this->getContainerBaseClass());

            $fresh = false;
        }

        require_once $cache->getPath();

        $this->container = new $class();
        $this->container->set('app', $this);

        if (!$fresh && $this->container->has('cache_warmer')) {
            $this->container->get('cache_warmer')->warmUp($this->container->getParameter('app.cache_dir'));
        }
    }

	protected function dumpContainer(ConfigCache $cache, ContainerBuilder $container, $class, $baseClass)
    {
        // cache the container
        $dumper = new PhpDumper($container);

        if (class_exists('ProxyManager\Configuration')) {
            $dumper->setProxyDumper(new ProxyDumper()); //Symfony\Bridge\ProxyManager
        }

        $content = $dumper->dump(array('class' => $class, 'base_class' => $baseClass));
        if (!$this->debug) {
            $content = self::stripComments($content);
        }

        $cache->write($content, $container->getResources());
    }

	/**
     * Removes comments from a PHP source string.
     *
     * We don't use the PHP php_strip_whitespace() function
     * as we want the content to be readable and well-formatted.
     *
     * @param string $source A PHP string
     *
     * @return string The PHP string with the comments removed
     */
    public static function stripComments($source)
    {
        if (!function_exists('token_get_all')) {
            return $source;
        }

        $rawChunk = '';
        $output = '';
        $tokens = token_get_all($source);
        for (reset($tokens); false !== $token = current($tokens); next($tokens)) {
            if (is_string($token)) {
                $rawChunk .= $token;
            } elseif (T_START_HEREDOC === $token[0]) {
                $output .= preg_replace(array('/\s+$/Sm', '/\n+/S'), "\n", $rawChunk).$token[1];
                do {
                    $token = next($tokens);
                    $output .= $token[1];
                } while ($token[0] !== T_END_HEREDOC);
                $rawChunk = '';
            } elseif (!in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
                $rawChunk .= $token[1];
            }
        }

        // replace multiple new lines with a single newline
        $output .= preg_replace(array('/\s+$/Sm', '/\n+/S'), "\n", $rawChunk);

        return $output;
    }

	public function getCacheDir()
    {
        return $this->rootDir.'/cache/'.$this->environment;
    }

	public function getLogDir()
    {
        return $this->rootDir.'/logs';
    }

	protected function buildContainer()
    {
        foreach (array('cache' => $this->getCacheDir(), 'logs' => $this->getLogDir()) as $name => $dir) {
            if (!is_dir($dir)) {
                if (false === @mkdir($dir, 0777, true)) {
                    throw new \RuntimeException(sprintf("Unable to create the %s directory (%s)\n", $name, $dir));
                }
            } elseif (!is_writable($dir)) {
                throw new \RuntimeException(sprintf("Unable to write in the %s directory (%s)\n", $name, $dir));
            }
        }

        $container = $this->getContainerBuilder();
        $container->addObjectResource($this);
        $this->prepareContainer($container);

        if (null !== $cont = $this->registerContainerConfiguration($this->getContainerLoader($container))) {
            $container->merge($cont);
        }

        $container->addCompilerPass(new AddClassesToCachePass($this));

        return $container;
    }

	protected function getContainerBuilder()
    {
        $container = new ContainerBuilder(new ParameterBag($this->getAppParameters()));

		if (class_exists('ProxyManager\Configuration')) {
            $container->setProxyInstantiator(new RuntimeInstantiator()); //Symfony\Bridge\ProxyManager
        }

        return $container;
    }

    protected function prepareContainer(ContainerBuilder $container)
    {
        $extensions = array();
        foreach ($this->modules as $module) {
            if ($extension = $module->getContainerExtension()) {
                $container->registerExtension($extension);
                $extensions[] = $extension->getAlias();
            }

            if ($this->debug) {
                $container->addObjectResource($module);
            }
        }
        foreach ($this->modules as $module) {
            $module->build($container);
        }

        // ensure these extensions are implicitly loaded
        $container->getCompilerPassConfig()->setMergePass(new MergeExtensionConfigurationPass($extensions));
    }

	protected function getContainerLoader(ContainerInterface $container)
    {
        $locator = new FileLocator(__DIR__);
        $resolver = new LoaderResolver(array(
            new XmlFileLoader($container, $locator),
            new YamlFileLoader($container, $locator),
            new IniFileLoader($container, $locator),
            new PhpFileLoader($container, $locator),
            new ClosureLoader($container),
        ));

        return new DelegatingLoader($resolver);
    }

	protected function setCacheClass($cacheClass)
    {
		if(!is_string($cacheClass)){
			throw new \InvalidArgumentException("In method Novice\Application::setCacheClass, argument 1 must be a string. ".ucfirst(gettype($cacheClass))." type given.");
		}
		
		$this->cacheClass = 'Doctrine\Common\Cache\ArrayCache';
	}

	protected function getCacheClass()
    {
		if(!isset($this->cacheClass)) {
			$this->setCacheClass('Doctrine\Common\Cache\ArrayCache');
		}

		return $this->cacheClass;
	}

	protected function getAppParameters()
    {
        $modules = array();
        foreach ($this->modules as $name => $module) {
            $modules[$name] = get_class($module);
        }

        return array_merge(
            array(
                'app.root_dir'        => $this->rootDir,
                'app.environment'     => $this->environment,
                'app.debug'           => $this->debug,
                'app.name'            => $this->name,
                'app.cache_dir'       => $this->getCacheDir(),
                'app.logs_dir'        => $this->getLogDir(),
                'app.modules'         => $modules,
                //'app.charset'         => $this->getCharset(),
                'app.container_class' => $this->getContainerClass(),
				'novice.cache'        => $this->getCacheClass(),
            ),
            $this->getEnvParameters()
        );
    }

	protected function getEnvParameters()
    {
        $parameters = array();
        foreach ($_SERVER as $key => $value) {
            if (0 === strpos($key, 'NOVICE__')) {
                $parameters[strtolower(str_replace('__', '.', substr($key, 9)))] = $value;
            }
        }

        return $parameters;
    }

	/**
     * Used in AddClassesToCachePass
     */
    public function setClassCache(array $classes)
    {
        file_put_contents($this->getCacheDir().'/classes.map', sprintf('<?php return %s;', var_export($classes, true)));
    }
  
  /*public function setUser($user)
  {   
  	if($user instanceof \Doctrine_Record)
	{return $this->user = $user ;}
	else{echo "InvalidArgumentException, user n'est pas une instance de Doctrine_Record (Application::setUser(user))"; exit;}
  }*/

  public function getControllerClass($controller)
  {
	$pos = strpos($controller, '::');
	if($pos === false){
		if(substr_count($controller, ':') != 2){
			throw new \InvalidArgumentException('In route "'.$result['_route'].'". 
			"_controller" value must respect syntax : 
			"Module:Controller:method" or "namespace\to\Controller::method".');
		}
		$pos = strrpos($controller, ':');
		$method = substr($controller, $pos+1);
		$module_class = substr($controller, 0 ,$pos);
		$pos = strrpos($module_class, ':');
		$class_name = substr($module_class, $pos+1);
		$module_name = substr($module_class, 0 ,$pos);
		$module = $this->getModule($module_name);
		$class=$module->getNamespace().'\Controller\\'.$class_name;
		//echo "module_name =  ".$module_name."<br>";
	}
	else
	{
		$method = substr($controller, $pos+2);
		$class = substr($controller, 0 ,$pos);
		$class = str_replace(":","\\", $class);
		$module_name = '';
	}
	return new $class($this->container, $module_name, $method);
	//echo "class =  ".$class."<br>";
	//echo "method =  ".$method."<br>";
	//exit(' - getControllerClass - line 339');
	
  }
  
  public function getOldController()
  {

	if (false === $this->booted) {
            $this->boot();
        }
	
	$router = $this->container->get('router');

	  $httpRequest = new HTTPRequest();
		
		if(null == $httpRequest->getContext()){
			$httpRequest->setContext($router->getContext());
		}
		
	  $this->httpRequest = $httpRequest;
	  $this->httpResponse = new HTTPResponse($this->container);
		
	try {
	 $result = $router->match($this->httpRequest->getPathInfo());
	}
	catch(\Symfony\Component\Routing\Exception\ResourceNotFoundException $e) {
		$this->httpResponse()->redirect404();
		//exit("404 NOT FOUND");
	}

	$route = $result['_route'];
	unset($result['_route']);

	if (!isset($result['_controller'])) {
            throw new NotFoundHttpException(sprintf('Unable to find the controller for route "%s".', $route));
        }

	$controller = $result['_controller'];
	unset($result['_controller']);
	
	$middlewares;
	if(isset($result['_middlewares'])) {
		$middlewares = $result['_middlewares'];
		unset($result['_middlewares']);
	}


	$_GET = array_merge($_GET, $result);




	//phpinfo();
	//$this->container->get('middlewares')->handle($this->httpRequest() , $this->httpResponse());

	return $this->getControllerClass($controller);

	//dump($result); exit;
	//return $response = call_user_func_array(array($this->getControllerClass($controller), "executeIndex"), array($result));
  }


  public function handle(Request $request , $handleException = false)
  {
	  /*dump($this->container->hasScope('request'));
	  dump($this->container);*/
	  //$this->getContainer()->enterScope('request');
	  /*$this->getContainer()->set('request', new Request(), 'request');
	  dump($this->getContainer()->get('request'));
	  dump($this->container->getNewsletterManagerService(false));
	  dump($this->getContainer()->get('mailer'));*/
	  //$this->getContainer()->set('request', $request, 'request');
	  
	  //dump($this->getContainer()->get('request'));
	  //$this->getContainer()->leaveScope('request');
	  //dump($this->container->getNewsletterManagerService(false));
	  //dump($this->getContainer()->get('mailer'));

	  //exit(__METHOD__);
	  
	  if (false === $this->booted) {
            $this->boot();
      }
	  return $this->container->get('http_app')->handle($request, $handleException);
  }

  private function handleRequest(Request $request)
  {
	$router = $this->container->get('router');
	$routerContext = $router->getContext()->fromRequest($request);
		
	$this->httpRequest = $request;
		
	try {
	 $result = $router->match($this->httpRequest->getPathInfo());
	}
	catch(\Symfony\Component\Routing\Exception\ResourceNotFoundException $e) {
		//var_dump($this->container->getServiceIds()); exit;
		$this->container->get('templating')->setContentFile('file:[errors]404.tpl');
		$rep = \Symfony\Component\HttpFoundation\StreamedResponse::create(array($this->container->get('templating'), "getGeneratedPage"),'404');
		return $rep->prepare($request);
	}

	$this->httpRequest()->attributes->add($result);

	$controller = $this->getController($this->httpRequest());

	unset($result['_route']);
	
	/*$middlewares;
	if(isset($result['_middlewares'])) {
		$middlewares = $result['_middlewares'];
		unset($result['_middlewares']);
	}*/

	//dump($this->httpRequest->attributes->all()); exit;

	$this->container->get('middlewares')->handle($this->httpRequest());

	// call controller
	//$response = $controller->execute();
    $response = call_user_func_array($controller, array());

	$this->container->get('middlewares')->postExecute($this->httpRequest() , $response);

	$this->container->set('middlewares','');

	return $response->prepare($request);
  }

  public function getController(Request $request)
    {
        if (!$controller = $request->attributes->get('_controller')) {
            throw new \RuntimeException(sprintf('Unable to find the controller for path "%s".', $request->getPathInfo())); //see HttpException
        }

        if (is_array($controller) || (is_object($controller) && method_exists($controller, '__invoke'))) {
            return $controller;
        }

        if (false === strpos($controller, ':')) {
            if (method_exists($controller, '__invoke')) {
                return new $controller;
            } elseif (function_exists($controller)) {
                return $controller;
            }
        }

        $callable = $this->createController($controller);

        if (!is_callable($callable)) {
            throw new \InvalidArgumentException(sprintf('The controller for URI "%s" is not callable.', $request->getPathInfo()));
        }

        return $callable;
    }


	protected function createController($controller)
    {	
		$modulename = '';
		if(strpos($controller, '::') === false && substr_count($controller, ':') == 2){
			list($modulename, $classname, $method) = explode(':', $controller, 3);
			if(!empty($this->moduleMap[$modulename])){
				$module = $this->getModule($modulename);
				$class = $module->getNamespace().'\Controller\\'.$classname;
				//$controller=$module->getNamespace().'\Controller\\'.$class.'::'.$method;
				//dump($class);dump($method); exit('-createController-');
				return array(new $class($this->container, $modulename, $method), 'execute'/*.ucfirst($method)*/);
			}
		}

        if (false === strpos($controller, '::')) {
            throw new \InvalidArgumentException(sprintf('Unable to find controller "%s".', $controller));
        }

        list($class, $method) = explode('::', $controller, 2);

		//dump($class);dump($method); exit('-createController-');

        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }

        return array(new $class($this->container, $modulename, $method), 'execute'.ucfirst($method));
    }
  
  abstract public function run();
  
  public function httpRequest()
  {
    return $this->container->get('request_stack')->getCurrentRequest();
  }

  public function setHttpRequest($req)
  {
    //$this->httpRequest = $req;
  }
  
  public function httpResponse()
  {
    return $this->httpResponse;
  }
  
  public function name()
  {
    return $this->name;
  }
}