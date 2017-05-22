<?php
namespace Rgs\CatalogModule\Validator;

use Novice\Form\Validator\SimpleValidator;
use Rgs\CatalogModule\Entity\Request as UserRequest;
use Symfony\Component\HttpFoundation\Request;
use Novice\Templating\Assignor\ErrorMessages;

class UserRequestValidator extends SimpleValidator
{
	
  public function validateRequest(Request $request, $object, ErrorMessages $assignment){
	  if($request->isMethod('POST') && $this->support($object))
	  {
		  $this->validate($object, $assignment);
		  return true;
	  }
	  return false;
  }
  
  public function validate($userRequest, ErrorMessages $assignment){	  
	  if(!$this->notNullValidate($userRequest->getSubject()))
	  	$assignment->set("subject", "The subject cannot be empty");
	
	  if(!$this->notNullValidate($userRequest->getDescription()))
	  	$assignment->set("description", "The description cannot be empty");
  }
  
  public function support($object){
	  return $object instanceof UserRequest;
  }
}