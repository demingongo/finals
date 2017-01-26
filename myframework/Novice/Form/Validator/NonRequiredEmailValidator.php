<?php
namespace Novice\Form\Validator;

use Novice\Form\Validator\EmailValidator;

class NonRequiredEmailValidator extends EmailValidator
{
  public function isValid($value)
  {
	if(empty($value)){
		return true;
	}
	
	return parent::isValid($value);
  }
}