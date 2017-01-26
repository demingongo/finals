<?php
namespace Novice\Annotation\Editor;

use Symfony\Component\HttpFoundation\Request;

class PropertyEditorRegistry
{
	
	private $editors = array();
	
	
	public function __construct(){
	}
	
	public function set($name, PropertyEditorInterface $editor){
		$this->editors[$name] = $editor;
		return $this;
	}
	
	public function get($name){
		return $this->editors[$name];
	}
	
	/**
	 * @param string  			 	 $name
	 * @param Request  			 	 $request
     * @param string   			 	 $propertyName
	 * @param string|numeric|array   $propertyValue
     * @param object   			 	 $attribute
     * @param ReflectionClass		 $attributeClass
	 *
	 * @return boolean
	 */
	public function edit($name, Request $request, $propertyName, $propertyValue, $attribute, \ReflectionClass $attributeClass)
	{
		$editor = $this->editors[$name];
		if(is_array($editor->editableClasses()) 
			&& !empty($editor->editableClasses()) 
				&& !in_array($attributeClass->getName(),$editor->editableClasses())){
			throw new \InvalidArgumentException(sprintf('@AttributeConverter: 
					Cannot use editor "%s" for class "%s" 
			as it\'s only usable by the following classe(s): 
			"%s"',$name,$attributeClass->getName(),implode(" ", $editor->editableClasses())));
		}
		
		if(is_array($editor->editableProperties()) 
			&& !empty($editor->editableProperties()) 
				&& !in_array($propertyName,$editor->editableProperties())){
			return false;
		}
		
		$editor->edit($propertyName, $propertyValue, $attribute);
		return true;
	}

}
