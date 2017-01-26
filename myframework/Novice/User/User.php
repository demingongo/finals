<?php
namespace Novice\User;

class User implements \ArrayAccess
{
  private $data;

  public function setData($user)
  {
	  //if(empty($this->data)){
		  $this->data = $user;
		  return $this;
	  //}
  }

  public function getData()
  {
	return $this->data;
  }

  public function offsetSet($offset, $value) {
    $method = 'set'.ucfirst($offset);
    if(isset($this->$offset) && is_callable(array($this, $method)))
	{
		$this->$method($value);
	}
  }

  public function offsetExists($offset) {
    $method = 'get'.ucfirst($offset);
	return (isset($this->$offset)) && is_callable(array($this, $method));
  }

  public function offsetGet($offset) {
	$method = 'get'.ucfirst($offset);
    if(isset($this->$offset) && is_callable(array($this, $method)))
	{
		return $this->$method();
	}
  }

  public function offsetUnset($offset) {
    throw new \Exception('Impossible de supprimer  l\'attribut "'.$offset.'" ainsi');
  }
}