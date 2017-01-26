<?php
namespace Novice\Entity;

use Symfony\Component\DependencyInjection\Container;

abstract class Entity implements \ArrayAccess
{
  protected $erreurs = array();
  
  public function erreurs()
  {
    return $this->erreurs;
  }
  
  public function offsetGet($var)
  {
	$camelized = $this->camelize($var);

	$method = 'get'.ucfirst($camelized);
	
    if (is_callable(array($this, $method)))
    {
      return $this->$method();
    }
	else if(is_callable(array($this, $var))){
		$rm = new \ReflectionMethod(get_class($this), $var);
		if (!$rm->getNumberOfRequiredParameters()) {
			//dump($var);
			return $this->$var();
		}
	}
  }
  
  public function offsetSet($var, $value)
  {
	$var = $this->camelize($var);
	
    $method = 'set'.ucfirst($var);
    
    if (is_callable(array($this, $method)))
    {
      $this->$method($value);
    }
  }
  
  public function offsetExists($var)
  {
	$var = $this->camelize($var);

	$r = new \ReflectionClass($this);

    return $r->hasProperty($var) && is_callable(array($this, 'get'.ucfirst($var)));
  }
  
  public function offsetUnset($var)
  {
    throw new \Exception('Impossible de supprimer une quelconque valeur');
  }

  private function camelize($scored) {
    return lcfirst(Container::camelize($scored));
	/*
	return lcfirst(
      implode(
        '',
        array_map(
          'ucfirst',
          array_map(
            'strtolower',
            explode(
              '_', $scored)))));
	*/
  }

  /*private function underscore($cameled) {
    return implode(
      '_',
      array_map(
        'strtolower',
        preg_split('/([A-Z]{1}[^A-Z]*)/', $cameled, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY)));
  }*/

  abstract public function isNew();
}