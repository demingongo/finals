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
function smarty_block_form_input($params, $content, \Smarty_Internal_Template $template, &$repeat)
{
	$modelAttribute = "_novice_plugin_smarty_block_form_var";
	
    // only output on the closing tag
    if(!$repeat){
		$value = "";
		$type = "text";
		
		$retour = '<input';
		if(isset($params['type'])){
			$type = $params['type'];
			unset($params['type']);
		}
		$retour .= ' type="'.$type.'"';
		
		if(isset($params['path'])){
			$name = $params['path'];
			$value = htmlspecialchars($template->getTemplateVars($modelAttribute)[$name]);
			$retour .= ' name="'.$name.'" value="'.$value.'"';
			unset($params['path']);
		}
		
		foreach($params as $kp => $vp){
			$retour .= ' '.$kp.'="'.$vp.'"';
		}
		
		$retour .= " />";
		if (isset($content)) {
            $retour .= $content;
        }
		return $retour;
    }
}
?>