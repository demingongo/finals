<?php
namespace Novice\Annotation\Editor;

use Symfony\Component\HttpFoundation\Request;

interface PropertyEditorInterface
{
	/**
     * @param string   			 	 $propertyName
	 * @param string|numeric|array   $propertyValue
     * @param object   			 	 $attribute
	 *
	 * @return void
	 */
	public function edit($propertyName, $propertyValue, $attribute);
	
	/**
	 * Returns an array of classes names to be edited by this editor or void for all classes
	 * (ex: return array("Namespace1\ClassName1", "Namespace2\ClassName2"); )
	 *
	 *
	 * @return array()|void
	 */
	public function editableClasses();
	
	/**
	 * Returns an array of properties names to be edited by this editor or void for all properties
	 * (ex: return array("articles", "children"); )
	 *
	 *
	 * @return array()|void
	 */
	public function editableProperties();
}
