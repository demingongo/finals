<?php
/**
 * Smarty plugin
 */

/**
 * Smarty {purify} function plugin
 * Type:     function<br>
 * Name:     purify<br>
 * Purpose:  purify string for clean html output
 *
 * @author   Demingongo-Litemo Stephane
 *
 * @param array                    $params   parameters
 *
 * @return string clean html input string
 */

function smarty_function_html_purifier($params, &$smarty)
{	
	if(empty($params['html']) || !is_string($params['html']))
		return "";

	return $smarty->getContainer()->get('html.purifier')->purify($params['html']);
}
?>