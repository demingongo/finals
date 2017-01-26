<?php
namespace Novice\Templating\Assignor;

use Symfony\Component\DependencyInjection\Container;

abstract class Assignor implements \ArrayAccess, AssignorInterface
{

  private $messages = array();
  
  public function getMessages()
  {
	  return $this->messages;
  }
  
  public function set($path, $message)
  {
      $this->messages[$path] = $message;
  }
  
  public function get($path)
  {
	  if(isset($this->messages[$path])){
    	return $this->messages[$path];
	  }
  }
  
  public function offsetGet($path)
  {
	return $this->get($path);
  }
  
  public function offsetSet($path, $value)
  {
	$this->set($path, $value);
  }
  
  public function offsetExists($path)
  {
	return !($this->get($path) === null);
  }
  
  public function offsetUnset($path)
  {
    if($this->offsetExists($path)){
		$this->set($path, null);
	}
  }
  
  public function isEmpty()
  {
	return empty($this->messages);
  }
  
  public function getVarname()
  {
	  $r = new \ReflectionObject($this);
	  return str_replace(array("\\","/"),"_",Container::underscore($r->getName()));
  }
  
  public function __toString()
  {
	  $retour = "";
	  foreach($this->messages as $path => $value)
	  {
		  $retour .= "<br/>".$value;
	  }
	  return $retour;
  }
}