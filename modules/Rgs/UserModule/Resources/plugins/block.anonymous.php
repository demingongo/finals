<?php
/**
 * Smarty plugin
 */

/**
 * Smarty anonymous block plugin
 *
 * @author Demingongo-Litemo Stephane
 *
 * @param array                    $params   parameters
 * @param string                   $content  contents of the block
 * @param Smarty_Internal_Template $template template object
 * @param boolean                  &$repeat  repeat flag
 *
 * @return  html http links to urls rel css
 */

function smarty_block_anonymous($params, $content, &$smarty, &$repeat)
{
	// n'affiche que lors de la balise fermante
	if(!$repeat){
		if(!$smarty->getContainer()->get('session')->isAuthenticated()){
			return $content;
		}
		return;
	}
}
?>