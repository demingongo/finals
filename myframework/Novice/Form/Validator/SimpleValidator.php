<?php
namespace Novice\Form\Validator;

use Novice\Templating\Assignor\ErrorMessages;

abstract class SimpleValidator extends Validator
{  
  public function __construct($errorMessage = null)
  {
	  parent::__construct($errorMessage);
  }
  
  protected function notNullValidate($value){
	  $v = new NotNullValidator("");
	  return $v->isValid($value);
  }
  
  protected function nonRequiredEmailValidate($value){
	  $v = new NonRequiredEmailValidator("");
	  return $v->isValid($value);
  }
  
  
  protected function emailValidate($value, $checkDNS = false, $strict = false){
	  $v = new EmailValidator("");
	  return $v->isValid($value, $checkDNS, $strict);
  }
  
  public function isValid($value){
	  throw new \Exception('Method "isValid()" not supported: use "validate($object, FormError $assignment)" or "validateRequest(Request $request, $object, FormError $assignment)" instead.');
  }
  
  abstract public function validate($object, ErrorMessages $assignor);
  
  /**
   * @return boolean
   */
  abstract public function support($object);
}