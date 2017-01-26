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
function smarty_block_form($params, $content, \Smarty_Internal_Template $template, &$repeat)
{
	$modelAttribute = "_novice_plugin_smarty_block_form_var";
	
    // only output on the closing tag
    if(!$repeat){
		if(isset($params['var'])){
			unset($params['var']);
		}
		$retour = '<form';
		
		foreach($params as $kp => $vp){
			$retour .= ' '.$kp.'="'.$vp.'"';
		}
		
		$retour .= ">";
		
        if (isset($content)) {
            $retour .= $content;
        }
		$retour .= "</form>";
		
		//if(!($template->getTemplateVars($modelAttribute) === null)){
		$template->clearAssign($modelAttribute);
		//}
		
		return $retour;
    }
	else{
		if(!empty($params['var'])){
			$template->assign($modelAttribute, $template->getTemplateVars($params['var']));
		}
		if(isset($params['var'])){
			unset($params['var']);
		}
	}
}
?>