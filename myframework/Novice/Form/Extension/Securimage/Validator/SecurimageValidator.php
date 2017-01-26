<?php
namespace Novice\Form\Extension\Securimage\Validator;

use Novice\Form\Validator\Validator;

class SecurimageValidator extends Validator
{

  private $namespace;

  public function __construct($errorMessage, $namespace = null)
  {
    parent::__construct($errorMessage);
    
    $this->namespace = $namespace;
  }

  public function isValid($value)
  {
	$securimage = new \Securimage();
	if($this->namespace != null){
		$securimage->setNamespace($this->namespace);
	}
	$retour = ($securimage->check($value) == true);
	return $retour;
  }
}