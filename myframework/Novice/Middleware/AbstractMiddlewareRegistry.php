<?php
namespace Novice\Middleware;

use Symfony\Component\DependencyInjection\ContainerAware,
	Symfony\Component\DependencyInjection\ContainerInterface;

use Novice\HTTPRequest,
	Novice\HTTPResponse;

use Symfony\Component\Routing\Generator\UrlGenerator;

abstract class AbstractMiddlewareRegistry extends ContainerAware
{
  protected $url;
  protected $middlewares;
  protected $in_use;

  public function __construct(ContainerInterface $container, array $middlewares)
  {
    parent::setContainer($container);

	$this->middlewares = $middlewares;
	$this->in_use = array();
  }

  public function handle($request)
  {
	  $this->url = $request->getPathInfo();

	  foreach ($this->middlewares as $pattern => $middlewares) {
			if(0 !== $this->match($pattern))
			{
				foreach ($middlewares as $key => $middleware) {
					try{
						$r = new \ReflectionClass(/*'\\'.*/$middleware);
						if ($r->isSubclassOf('Novice\\Middleware\\Middleware') && !$r->isAbstract() && $r->getConstructor()->getNumberOfRequiredParameters() == 1) {
							$class = $r->newInstanceArgs(array($this->container));
							$class->handle($request);
							$this->in_use[] = $class;
							unset($middlewares[$key]);
						}
					}
					catch(\ReflectionException $e){
						throw new \Exception('Middleware class \''.$middleware.'\' does not exist '.$e);
					}
				}
			}
	  }
  }

  public function postExecute( $request , $response)
  {
	  foreach ($this->in_use as $class) {
			//if(method_exists($class , 'finalize' ))
			//{
				$class->postExecute($request , $response);
			//}
	  }
	  
  }

  private function match($pattern)
  {
    return preg_match("`".$pattern."`", $this->url, $matches);
  }

}
