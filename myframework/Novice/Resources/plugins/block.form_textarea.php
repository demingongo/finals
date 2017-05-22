<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     block.form.php
 * Type:     block
 * Name:     form_textarea
 * Purpose:  build an HTML textarea
 * -------------------------------------------------------------
 */
function smarty_block_form_textarea($params, $content, \Smarty_Internal_Template $template, &$repeat)
{
	$modelAttribute = "_novice_plugin_smarty_block_form_var";
	
    // only output on the closing tag
    if(!$repeat){
		$value = "";
		
		$retour = '<textarea';
		
		foreach($params as $kp => $vp){
			$retour .= ' '.$kp.'="'.$vp.'"';
		}

		if(isset($params['path'])){
			$name = $params['path'];
			$value = htmlspecialchars($template->getTemplateVars($modelAttribute)[$name]);
			$retour .= ' name="'.$name.'"';
			unset($params['path']);
		}
		
		$retour .= ">";

		$retour .= $value;

		if (isset($content) && trim($content) != "") {
            $retour .= $content;
        }
		$retour .= "</textarea>";
		return $retour;
    }
}
?>