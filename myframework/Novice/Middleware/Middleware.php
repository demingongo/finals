<?php
namespace Novice\Middleware;

use Symfony\Component\DependencyInjection\ContainerAware,
	Symfony\Component\DependencyInjection\ContainerInterface;

use Novice\HTTPRequest,
	Novice\HTTPResponse;

use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\HttpFoundation\RequestMatcher;
use Symfony\Component\HttpFoundation\Request;

abstract class Middleware extends ContainerAware
{

  private $name;
  private $requestMatcher;
  
  public function __construct(ContainerInterface $container)
  {
    parent::setContainer($container);
	$this->name = '';
	$this->requestMatcher = '';
  }

  public function handle(Request $request){}

  public function onRequest($event){}  //new GetResponseEvent($this, $request, $type)

  public function onController($event){} //new FilterControllerEvent($this, $controller, $request, $type);

  public function onView($event){} //new GetResponseForControllerResultEvent($this, $request, $type, $response)

  public function onResponse($event){} //new FilterResponseEvent($this, $request, $type, $response)

  public function terminate($event){} //new PostResponseEvent($this, $request, $response)

  public function postExecute( $request , $response){} //conclude

  public function generateUrl($name, $parameters = array(), $referenceType = UrlGenerator::ABSOLUTE_PATH)
  {
		return $this->container->get('router')->generate($name, $parameters, $referenceType);
  }

  public function assign($varname, $var = null, $nocache = false)
  {
		return $this->container->get('templating')->assign($varname, $var, $nocache);
  }

  public function redirect($location)
  {
		//return $this->container->get('app')->httpResponse()->redirect($location);
		$response = new RedirectResponse($location);
		return $response->send();
  }

  public function redirectError($filename , $header = '')
  {
		return $this->container->get('app')->httpResponse()->redirectError($filename , $header);
  }

  public function httpRequest()
  { 
    return $this->get('app')->httpRequest();
  }

  public function httpResponse()
  { 
    return $this->get('app')->httpResponse();
  }

  public function has($id)
  {
        return $this->container->has($id);
  }

  public function get($id, $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE)
  {
		return $this->container->get($id, $invalidBehavior);
  }

  public function getName()
  {
		return $this->name;
  }

  public function getRequestMatcher()
  {
	return $this->requestMatcher;
  }

  public function matches(Request $request)
  {
	return $this->requestMatcher->matches($request);
  }

  public static function getSubscribedEvents()
  {
      return array(
          AppEvents::REQUEST => array('onRequest', 128),
      );
  }
}
