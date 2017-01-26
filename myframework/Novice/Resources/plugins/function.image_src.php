<?php
/**
 * Smarty plugin
 */

/**
 * Smarty {asset} function plugin
 * Type:     function<br>
 * Name:     image_src<br>
 * Purpose:  
 *
 * @author   Demingongo-Litemo Stephane
 *
 * @param array                    $params   parameters
 *
 * @return src|null
 */

function smarty_function_image_src($params, &$smarty)
{

	$url;

	if(empty($params['path']))
	{
		if($smarty->getContainer()->hasParameter('templating.plugin.image_src.no_image')){
			$path = $smarty->getContainer()->getParameter('templating.plugin.image_src.no_image');
			$url = $smarty->getAssets()->getUrl($path, null);
		}
		else
			$url = "";
	}
	else
	{
		if(!isset($params['package'])){
			$params['package'] = null;
		}

		try{
			$url = $smarty->getAssets()->getUrl($params['path'], $params['package']);
		}
		catch(\Exception $e){
			if($smarty->getContainer()->hasParameter('templating.plugin.image_src.image_not_found')){
				$path = $smarty->getContainer()->getParameter('templating.plugin.image_src.image_not_found');
				$url = $smarty->getAssets()->getUrl($path, null);
			}
			else
				$url = "";
		}
	}

	return $url;
}
?>