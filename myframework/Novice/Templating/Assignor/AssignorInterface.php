<?php
namespace Novice\Templating\Assignor;

interface AssignorInterface
{ 
	/**
	 * If it returns a string (ex: "varname"), assigning this object will 
	 * assign it in the template with the string given (ex: $varname (which is this object)).
	 * If it returns an array, it must be an associative array with keys as varnames.
	 *
	 * @return string|array
	 */
	public function getVarname();
}