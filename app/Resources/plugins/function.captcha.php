<?php
/**
 * Smarty plugin
 */

/**
 * Smarty {captcha} function plugin
 * Type:     function<br>
 * Name:     captcha<br>
 * Purpose:  captcha in template
 *
 * Params:
 * <pre>
 * - message	- string 
 * - class      - string
 * </pre>
 *
 * @author   Demingongo-Litemo Stephane
 *
 * @param array                    $params   parameters
 *
 * @return string  The generated HTML code for displaying the captcha
 */

function smarty_function_captcha($params, &$smarty)
{	
	return \Securimage::getCaptchaHtml($params);
}

?>