<?php
namespace Novice\Form\Validator;

use Egulias\EmailValidator\EmailValidator as EguliasValidator;

class EmailValidator extends Validator
{

  protected $checkDNS;
  protected $strict;

  public function __construct($errorMessage, $checkDNS = false, $strict = false)
  {
    parent::__construct($errorMessage);
    
    $this->checkDNS = (bool) $checkDNS;
	$this->strict = (bool) $strict;
  }


  public function isValid($value)
  {
	$validator = new EguliasValidator();
	if(!($retour = $validator->isValid($value, $this->checkDNS, $this->strict))){
		
		//dump($validator->getWarnings());
		//dump($validator->getError());
	}
    
	return $retour;
  }
}