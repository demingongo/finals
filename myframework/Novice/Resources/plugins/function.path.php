<?php
/**
 * Smarty plugin
 */

/**
 * Smarty {path} function plugin
 * Type:     function<br>
 * Name:     path<br>
 * Purpose:  
 *
 * @author   Demingongo-Litemo Stephane
 *
 * @param array                    $params   parameters
 *
 * @return url|null
 */

function smarty_function_path($params, &$smarty)
{

	$url;

	if(empty($params['id']))
	{
		throw new \Exception("[plugin] No parameter 'id'");
	}
	else{
		$id = $params['id'];
		unset($params['id']);
		//dump($params);
		if(!isset($params['absolute'])){
			$referenceType = \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_PATH;
		}
		else{
			$referenceType = $params['absolute'];
			unset($params['absolute']);
		}
		if(isset($params['params']) && is_array($params['params'])){
			$params = $params['params'];
		}
		
		if(isset($params['referenceType'])){
			$referenceType = $params['referenceType'];
			unset($params['referenceType']);
		}
		
		try{
			$url = $smarty->getContainer()->get('router')->generate($id,$params,$referenceType);
		}
		catch(\Symfony\Component\Routing\Exception\ExceptionInterface $e){
			trigger_error($e->getMessage());
		}
	}

	if(isset($params['as']) && is_string($params['as'])){
		$url = $params['as']."=\"".$url."\"";
	}

	return $url;

}
?>