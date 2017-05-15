<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     block.if_not_expired.php
 * Type:     block
 * Name:     if_front_reservation
 * -------------------------------------------------------------
 */
function smarty_block_if_not_expired($params, $content, \Smarty_Internal_Template $template, &$repeat)
{	
    // only output on the closing tag
    if(!$repeat && isset($params['from'])){
		$date = $params['from'];
		$dt = new \Datetime("now");
		
		if($date > $dt && isset($content)){
			return $content;
		}
    }
}
?>