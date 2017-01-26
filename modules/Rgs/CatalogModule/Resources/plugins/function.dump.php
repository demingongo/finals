<?php


function smarty_function_dump($params, &$smarty)
{	
	dump(__FILE__);
	dump("smarty_function_dump");
	exit(__FILE__);
}

?>