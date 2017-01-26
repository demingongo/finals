<?php
namespace Rgs\CatalogModule\Validator;

use Novice\Form\Validator\SimpleValidator;
use Rgs\CatalogModule\Entity\Article;
use Symfony\Component\HttpFoundation\Request;
use Novice\Templating\Assignor\ErrorMessages;

class ArticleValidator extends SimpleValidator
{
	
  public function validateRequest(Request $request, $object, ErrorMessages $assignment){
	  if($request->isMethod('POST') && $this->support($object))
	  {
		  return $this->validate($object, $assignment);
	  }
  }
  
  public function validate($article, ErrorMessages $assignment){
	  /*if(!$this->support($article))
	  	return;*/
	  
	  if(!$this->notNullValidate($article->getName()))
	  	$assignment->set("name", "Le nom est requis");
  }
  
  public function support($object){
	  return $object instanceof Article;
  }
}