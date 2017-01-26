<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     block.form.php
 * Type:     block
 * Name:     form
 * Purpose:  build an HTML form
 * -------------------------------------------------------------
 */
function smarty_block_form_options($params, $content, \Smarty_Internal_Template $template, &$repeat)
{
	$modelAttribute = "_novice_plugin_smarty_block_form_var";
	
	$modelSelectPath = "_novice_plugin_smarty_block_form_select_path";
	
    // only output on the closing tag
    if(!$repeat){
		$items = array();
		$itemLabel;
		$itemValue;
		$value = "";
		
		if(!empty($params['items']) && !empty($params['itemLabel']) && !empty($params['itemValue'])){
			$items = $params['items'];
			unset($params['items']);
			$itemLabel = $params['itemLabel'];
			unset($params['itemLabel']);
			$itemValue = $params['itemValue'];
			unset($params['itemValue']);
		}
		/*else if(!empty($params['items']) &&is_array($params['items'])){
			$items = $params['items'];
			unset($params['items']);
		}*/
		else{
			return;
		}
		
		$selectValue = array();
		if(!($template->getTemplateVars($modelSelectPath) === null)){
			$modelValues = $template->getTemplateVars($modelSelectPath);
			if(is_array($modelValues)){
				foreach($modelValues as $m){
					$selectValue[] = $m[$itemValue];
				}
			}
			else{
				$selectValue = $modelValues[$itemValue];
			}
		}
		
		//foreach($items as $k => $i){
		$retour = "";
		foreach($items as $i){
			$retour .= '<option';
			
			$retour .= ' value="'.htmlspecialchars($i[$itemValue]).'"';
			
			if((!is_array($selectValue) && $selectValue == $i[$itemValue])
				|| (is_array($selectValue) && in_array($i[$itemValue], $selectValue))){
				$retour .= ' selected="selected"';
			}
				
			$retour .= ">";
			
			$retour .= $i[$itemLabel];
			
			$retour .= "</option>";
		}
		
		return $retour;
    }
}
?>