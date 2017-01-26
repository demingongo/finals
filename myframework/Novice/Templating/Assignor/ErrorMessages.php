<?php
namespace Novice\Templating\Assignor;

use Symfony\Component\DependencyInjection\Container;

class ErrorMessages extends Assignor //implements \ArrayAccess, AssignmentInterface
{

  /*private $messages = array();
  
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
  }*/
  
  public function hasError()
  {
	return !$this->isEmpty();
  }
  
  public function getVarname()
  {
	  return '_'.parent::getVarname();
  }
}