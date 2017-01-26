<?php
/**
 * Smarty plugin
 */

/**
 * Smarty {asset} function plugin
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

function smarty_function_asset($params, &$smarty)
{

	$url;

	if(empty($params['url']))
	{
		throw new \Exception("[plugin]{asset} No parameter 'url'");
	}
	else{
		if(!isset($params['package'])){
			$params['package'] = null;
		}

		try{
			$url = $smarty->getAssets()->getUrl($params['url'], $params['package']);
		}
		catch(\Exception $e){
			trigger_error($e->getMessage());
		}
	}

	return $url;

}
?>