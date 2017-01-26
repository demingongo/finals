<?php
/**
 * Smarty plugin
 */

/**
 * Smarty {constant} function plugin
 * Type:     function<br>
 * Name:     constant<br>
 * Purpose:  
 *
 * @author   Demingongo-Litemo Stephane
 *
 * @param array                    $params   parameters
 *
 * @return constant value
 */

function smarty_function_constant($params, &$smarty)
{
	if(empty($params['name']) || empty($params['object'])){
		try{
			throw new \Exception("[plugin]{constant} Need parameters 'name' and 'object'");
		}
		catch(\Exception $e){
			trigger_error($e->getMessage());
		}		
	}
	else{
		$object = $params['object'];
		$name = $params['name'];
		if(!is_object($object)){
			try{
				throw new \Exception("[plugin]{constant} parameter 'object' must be an object");
			}
			catch(\Exception $e){
				trigger_error($e->getMessage());
			}
		}
		
		try{
			$r = new ReflectionObject($object);		
			$const = $r->getConstant($name);
			return $const;
		}
		catch(\Exception $e){
				trigger_error($e->getMessage());
		}		
	}
}
?>