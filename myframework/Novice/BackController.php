<?php
namespace Novice;

use Symfony\Component\DependencyInjection\Container,
	Symfony\Component\DependencyInjection\ContainerAware,
	Symfony\Component\DependencyInjection\ContainerInterface;
use Novice\Form\FormBuilder;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class BackController extends ContainerAware
{
  private $module;
  protected $action;
  protected $view;
  protected $ext;
  
  //protected $script = '';
  
  public function __construct(ContainerInterface $container, $module, $action, $ext = 'tpl')
  {
    parent::setContainer($container);

	$this->ext = $ext;

    $this->setModule($module);
    $this->setAction($action);
    //$this->setView($action); //Container::underscore($action)
  }
  
  public function execute($request)
  {
    $method = 'execute'.ucfirst($this->action);

    if (!is_callable(array($this, $method)))
    {
      throw new \RuntimeException('L\'action "'.$this->action.'" n\'est pas définie sur ce controller');
    }
    
    $response = $this->$method($request);

	if(!is_object($response) || !$response instanceof \Symfony\Component\HttpFoundation\Response){
		$response = $this->createHttpResponse();
	}

	return $response;
  }
  
  private function setModule($module)
  { 
    $this->module = $module;
  }
  
  public function getModule()
  { 
    return $this->module;
  }
  
  public function setAction($action)
  {
    if (!is_string($action) || empty($action))
    {
      throw new \InvalidArgumentException('L\'action doit être une chaine de caractères valide');
    }
    
    $this->action = $action;
  }
  
  public function setTemplate($template){
	  $this->container->get('templating')->setContentFile($template);
  }
  
  public function setView($view)
  {
	if (!is_string($view) || empty($view))
    {
      throw new \InvalidArgumentException('La vue doit être une chaine de caractères valide');
    }

	$pos = strrpos($view, '.');
	if ($pos === false) {
		$view = $view.'.'.$this->ext;
	}
	
	$this->setTemplate($view);
    /*if (!is_string($view) || empty($view))
    {
      throw new \InvalidArgumentException('La vue doit être une chaine de caractères valide');
    }

	$pos = strrpos($view, '.');
	if ($pos === false) {
		$view = $view.'.'.$this->ext;
	}

	$reflected = new \ReflectionObject($this);
	$view = dirname($reflected->getFileName()).'/../views/'.$this->view;*/
	/*if(!isset($this->view)){
		if(!empty($this->module)){
			$this->view = 'file:['.$this->module.']'.$view;
		}
		else{
			$this->view = 'file:'.$view;
		}
	}
	else{
		$this->view = $view;
	}
    $this->container->get('templating')->setContentFile($this->view);*/
  }

  /*public function getUser()
    {
        if (!$this->has('session')) {
            return null;
		}

        if (!is_object($user = $this->get('session')->getUser())) {
            return null;
        }

        return $user;
    }*/

  public function buildForm(FormBuilder $formBuilder)
  {
	  $formBuilder->setContainer($this->container);
	  $formBuilder->build();
	  return $formBuilder;
  }

  public function generateUrl($name, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
  {
		return $this->get('router')->generate($name, $parameters, $referenceType);
  }

  public function assign($varname, $var = null, $nocache = false)
  {
		return $this->get('templating')->assign($varname, $var, $nocache);
  }

  public function isAssigned($varname)
  {
		return !($this->get('templating')->getTemplateVars($varname) === null);
  }

  public function isNotAssigned($varname)
  {
		return $this->get('templating')->getTemplateVars($varname) === null;
  }

  public function getDoctrine()
  {
		return $this->get('managers');
  }

  public function getDoctrineManager($name = '')
  {
		return $this->getDoctrine()->getManager($name);
  }

  public function redirect($location)
  {
		return $response = new RedirectResponse($location);
  }

  public function redirectNow($location)
  {
		$response = new RedirectResponse($location);
		return $response->send();
  }

  public function redirectError($status = 404)
  {
		//return $this->httpResponse()->redirectError($filename , $header);
		/*$this->container->get('templating')->setContentFile('file:[errors]'.$filename.'.'.$this->ext);
		$rep = \Symfony\Component\HttpFoundation\StreamedResponse::create(array($this->container->get('templating'), "getGeneratedPage"),$filename);
		return $rep->prepare($this->container->get('request'));*/

		return \Novice\StreamedErrorResponse::createResponse($this->container->get('templating'), $status);
  }

  public function httpRequest()
  { 
    return $this->get('app')->httpRequest();
  }

  public function createHttpResponse($status = 200, $headers = array())
  { 
    $response = new \Symfony\Component\HttpFoundation\StreamedResponse(null, $status, $headers);
	$response->setCallback(array($this->container->get('templating'), "getGeneratedPage"));
	return $response;
  }

  public function has($id)
  {
        return $this->container->has($id);
  }

  public function get($id, $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE)
  {
		return $this->container->get($id, $invalidBehavior);
  }

  
}
