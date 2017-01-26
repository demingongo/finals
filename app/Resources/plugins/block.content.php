<?php
/**
 * Smarty plugin
 */

/**
 * Smarty stylesheets block plugin
 * Type:     block<br>
 * Name:     stylesheets<br>
 * Purpose:  link stylesheets
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

function smarty_block_content($params, $content, &$smarty, &$repeat)
{
	// n'affiche que lors de la balise fermante
	if(!$repeat){
	if(!empty($params)){
		echo "<div>params: <ol>";
	foreach($params as $k => $v)
	{
		echo "<li>".$k.": ".$v."</li>";
	}
	echo "</ol></div>";
	}

	return "<div>content: ".$content."</div>";
	}
	//return $retour;
}
?>