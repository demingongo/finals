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
function smarty_block_form_select($params, $content, \Smarty_Internal_Template $template, &$repeat)
{
	$modelAttribute = "_novice_plugin_smarty_block_form_var";
	
	$modelSelectPath = "_novice_plugin_smarty_block_form_select_path";
	
    // only output on the closing tag
    if(!$repeat){
		$value = "";
		
		$retour = '<select';
		
		if(!empty($params['path'])){
			$name = $params['path'];
			//$value = htmlspecialchars($template->getTemplateVars($modelAttribute)[$name]);
			$retour .= ' name="'.$name.'"';
			unset($params['path']);
		}
		
		foreach($params as $kp => $vp){
			$retour .= ' '.$kp.'="'.$vp.'"';
		}
		
		$retour .= ">";
		if (isset($content)) {
            $retour .= $content;
        }
		$retour .= "</select>";
		
		$template->clearAssign($modelSelectPath);
		
		return $retour;
    }
	else{
		if(!empty($params['path'])){
			$name = $params['path'];
			$template->assign($modelSelectPath, $template->getTemplateVars($modelAttribute)[$name]);
		}
		if(isset($params['path'])){
			unset($params['path']);
		}
	}
}
?>