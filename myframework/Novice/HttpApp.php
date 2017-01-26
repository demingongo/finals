<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Novice;

/*use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
*/
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Novice\Events;
use Novice\Middleware\Event;
use Novice\Middleware\MiddlewareDispatcher;


class HttpApp //implements HttpKernelInterface, TerminableInterface
{
    protected $dispatcher;
    protected $resolver;
	//protected $container;

    /**
     * Constructor
     *
     * @param EventDispatcherInterface    $dispatcher An EventDispatcherInterface instance
     * @param ControllerResolverInterface $resolver   A ControllerResolverInterface instance
     *
     * @api
     */
    public function __construct(MiddlewareDispatcher $dispatcher/*, ControllerResolverInterface $resolver*/)
    {
        $this->dispatcher = $dispatcher;
        //$this->resolver = $resolver;
    }

  public function getModule($name)
  {
    return $this->dispatcher->getContainer()->get('app')->getModule($name);
  }

  public function handle(Request $request, $catch = false)
  {
	  try{
		return $this->handleRequest($request);
	  }
	  catch (\Exception $e) {
            if (false === $catch) {
                throw $e;
            }

            return $this->handleException($e, $request);
      }
  }

  private function handleRequest(Request $request)
  {
	  
	$request->setSession($this->dispatcher->getContainer()->get('session'));
	$router = $this->dispatcher->getContainer()->get('router');
	$routerContext = $router->getContext()->fromRequest($request);

	$this->dispatcher->getContainer()->get('app')->setHttpRequest($request);
	
	//$this->dispatcher->getContainer()->enterScope('request');
	//$this->dispatcher->getContainer()->set('request', $request, 'request');
	
	$this->dispatcher->getContainer()->get('request_stack')->push($request);
		
	try {
	 $result = $router->match($request->getPathInfo());
	}
	catch(\Symfony\Component\Routing\Exception\ResourceNotFoundException $e) {
		/*$this->dispatcher->getContainer()->get('templating')->setContentFile('file:[errors]404.tpl');
		$rep = \Symfony\Component\HttpFoundation\StreamedResponse::create(array($this->dispatcher->getContainer()->get('templating'), "getGeneratedPage"),'404');*/
		$rep = StreamedErrorResponse::createResponse($this->dispatcher->getContainer()->get('templating')/*,'404'*/);
		return $this->filterResponse($rep, $request);
	}

	$request->attributes->add($result);

	// request
    $event = new \Novice\Event\GetResponseEvent($this, $request);
    $this->dispatcher->dispatchMiddlewares(Events::REQUEST, $event);
    if ($event->hasResponse()) {
        return $this->filterResponse($event->getResponse(), $request);
    }

	// load controller
	$controller = $this->getController($request);
	
	/** find a way to load Novice Annotations here **/

	$event = new \Novice\Event\FilterControllerEvent($this, $controller, $request);
    $this->dispatcher->dispatchMiddlewares(Events::CONTROLLER, $event);
    $controller = $event->getController();
	
	
	//test
	//dump(__METHOD__);
	
	/*
	$test = new Annotation\AnnotationLoader();
	$test->setContainer($this->dispatcher->getContainer());
	$test->setTemplating($this->dispatcher->getContainer()->get('templating'));
	$test->load($controller[0]);
	*/
	
	$arguments = $this->getArguments($request, $controller);

	// call controller
    //$response = call_user_func_array($controller, array($request));
	$response = call_user_func_array($controller, $arguments);

	// view
      if (!$response instanceof Response) {
            $event = new \Novice\Event\GetResponseForControllerResultEvent($this, $request, $response);
            $this->dispatcher->dispatchMiddlewares(Events::VIEW, $event);

            if ($event->hasResponse()) {
                $response = $event->getResponse();
            }

            if (!$response instanceof Response) {
                $msg = sprintf('The controller must return a response (%s given).', $this->varToString($response));

                // the user may have forgotten to return something
                if (null === $response) {
                    $msg .= ' Did you forget to add a return statement somewhere in your controller?';
                }
                throw new \LogicException($msg);
            }
      }

	return $this->filterResponse($response, $request);

	exit('httpApp');

	/*$this->container->get('middlewares')->postExecute($this->httpRequest() , $response);
	$this->container->set('middlewares','');

	return $response->prepare($request);*/
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
		
		/*dump($callable);
		exit(__METHOD__);*/
		
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
				$module = $this->getModule($modulename);
				$class = $module->getNamespace().'\Controller\\'.$classname;
				//$controller=$module->getNamespace().'\Controller\\'.$class.'::'.$method;
				//dump($class);dump($method); exit('-createController-');
				return array(new $class($this->dispatcher->getContainer(), $modulename, $method), 'execute'.ucfirst($method));
		}

        if (false === strpos($controller, '::')) {
            throw new \InvalidArgumentException(sprintf('Unable to find controller "%s".', $controller));
        }

        list($class, $method) = explode('::', $controller, 2);

		//dump($class);dump($method); exit('-createController-');

        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }
		
		$r = new \ReflectionClass($class);
		$callback = null;
		if($r->getParentClass()->getName() == "Novice\BackController"){
			/*$pos = strpos($method, 'execute');
			if($pos === 0){
				$method = substr($method, 7);
				$method = strtolower(substr($method, 0, 1)) . substr($method, 1);
			}*/			
			$callback = array(new $class($this->dispatcher->getContainer(), $modulename, $method), $method);
		}
		elseif(in_array("Symfony\Component\DependencyInjection\ContainerAwareInterface" , $r->getInterfaceNames())){
			$instance = new $class();
			$instance->setContainer($this->dispatcher->getContainer());
			$callback = array($instance, $method);
		}
		else{
			$callback = array(new $class(), $method);
		}
		
		/*dump($callback);
		exit(__METHOD__);*/

        return $callback;
    }
	
	protected function getArguments(Request $request, callable $controller)
	{
		$objet;
		$methodName;
		if(is_object($controller) && method_exists($controller, '__invoke'))
		{
			$objet = $controller;
			$methodName = '__invoke';
		}
		else if(is_array($controller)){
			$objet = $controller[0];
			$methodName = $controller[1];
		}
		else{
			return array($request);
		}
		
		if (!is_object($objet)) {
			return;
        }
		
		$class = new \ReflectionObject($objet);
        if ($class->isAbstract()) {
            throw new \InvalidArgumentException(sprintf('Cannot use class "%s" as controller as it is abstract.', $class->getName()));
        }

		$method = $class->getMethod($methodName);
		
		$args = array();
		
		foreach($method->getParameters() as $param)
		{
			if(isset($args[$param->getName()])){
				throw new \LogicException(sprintf('Multiple parameters "%s" in "%s::%s".', $param->getName(), $class->getName(),$method->getName()));
			}
			if($param->getName() == "request"){
				$args["request"] = $request;
			}
			else if($request->attributes->has($param->getName())){
				$args[$param->getName()] = $request->attributes->get($param->getName());
			}
            else if ($param->isDefaultValueAvailable()) {
				$args[$param->getName()] = $param->getDefaultValue();
            }
			else{
				throw new \Exception(
					sprintf('Cannot find a value for parameter "%s" for "%s::%s".', $param->getName(), $class->getName(),$method->getName())
				);
			}
		}
		
		return $args;
	}

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function terminate(Request $request, Response $response)
    {
        $this->dispatcher->dispatch(Events::TERMINATE, new \Novice\Event\PostResponseEvent($this, $request, $response));
    }

    private function filterResponse(Response $response, Request $request)
    {
        $event = new \Novice\Event\FilterResponseEvent($this, $request, $response);

        $this->dispatcher->dispatchMiddlewares(Events::RESPONSE, $event);

        return $event->getResponse();
    }

    private function handleException(\Exception $e, $request)
    {
        $event = new \Novice\Event\GetResponseForExceptionEvent($this, $request, $e);
        $this->dispatcher->dispatch(Events::EXCEPTION, $event);

        // a listener might have replaced the exception
        $e = $event->getException();

        if (!$event->hasResponse()) {
            throw $e;
        }

        $response = $event->getResponse();

        // the developer asked for a specific status code
        if ($response->headers->has('X-Status-Code')) {
            $response->setStatusCode($response->headers->get('X-Status-Code'));

            $response->headers->remove('X-Status-Code');
        } elseif (!$response->isClientError() && !$response->isServerError() && !$response->isRedirect()) {
            // ensure that we actually have an error response
            if ($e instanceof HttpExceptionInterface) {
                // keep the HTTP status code and headers
                $response->setStatusCode($e->getStatusCode());
                $response->headers->add($e->getHeaders());
            } else {
                $response->setStatusCode(500);
            }
        }

        try {
            return $this->filterResponse($response, $request);
        } catch (\Exception $e) {
            return $response;
        }
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
}
