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

function smarty_function_form_submit($params, &$smarty)
{
	if(isset($params['value'])){ 
		$value = $params['value'];
		unset($params['value']);
	}
	else{
		$value='Submit';
	}
	
	$retour = '<input type="submit" value="'.$value.'"';
	
	foreach($params as $kp => $vp){
		$retour .= ' '.$kp.'="'.$vp.'"';
	}
	
	$retour .= ' />';
	
	return  $retour;
}
?>