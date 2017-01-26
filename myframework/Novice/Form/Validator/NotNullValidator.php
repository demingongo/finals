<?php
namespace Novice\Form\Validator;

class NotNullValidator extends Validator
{
  public function isValid($value)
  {
	$closure = function($v){
		return ($v != '' && !ctype_space($v));
	};

	if(!is_array($value)){
		return $closure($value);
	}
	else{
		foreach($value as $v){
			if($closure($v)){
				return true;
			}
		}
	}
	return false;
  }
}