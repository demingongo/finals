<?php
/**
 * Smarty plugin
 */

/**
 * Smarty {captcha} function plugin
 * Type:     function<br>
 * Name:     filemanager_16_9<br>
 * Purpose:  filemanager 16:9 aspect ratio
 *
 * Params:
 * <pre>
 * - hrefDir		- string 
 * - query      - array
 * </pre>
 *
 * @author   Demingongo-Litemo Stephane
 *
 * @param array                    $params   parameters
 *
 * @return string  The generated HTML code for displaying the captcha
 */

function smarty_function_filemanager_16_9($params, &$smarty)
{	
	if(!isset($params['base_url'])){
		$params['base_url'] = $smarty->getContainer()->get('request_stack')->getCurrentRequest()->getBaseUrl();
	}

	if(!isset($params['filemanager_path'])){
		$params['filemanager_path'] = '/plugins/filemanager/filemanager';
	}

	if(isset($params['akey']) && is_string($params['akey'])){
		$params['akey'] = md5($params['akey']);
	}

	$path = \Novice\Form\Extension\Filemanager\Filemanager::getPath($params);

	return '<div class="embed-responsive embed-responsive-16by9">
  <iframe class="embed-responsive-item" src="'.htmlspecialchars($path).'"></iframe>
</div>';
	
}

?>