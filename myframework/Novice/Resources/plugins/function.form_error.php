<?php
/**
 * Smarty plugin
 */

/**
 * Smarty {constant} function plugin
 * Type:     function<br>
 * Name:     form<br>
 * Purpose:  
 *
 * @author   Demingongo-Litemo Stephane
 *
 * @param array                    $params   parameters
 *
 * @return constant value
 */

function smarty_function_form_error($params, &$smarty)
{
	$formError = "_novice_templating_assignor_error_messages";
	$formError = $smarty->getTemplateVars($formError);
	
	$path = $params["path"];
	unset($params["path"]);
	
	$retour = '<span';
	
	foreach($params as $kp => $vp){
		$retour .= ' '.$kp.'="'.$vp.'"';
	}
	
	$retour .= '>';
	
	$retour .= $formError[$path];
	
	$retour .= '</span>';
	
	return $retour;
}
?>