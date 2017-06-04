<?php


function smarty_function_select_quantity($params, &$smarty)
{	
	
	$min = isset($params["min"]) ? (int)$params["min"] : 1;
	$max = isset($params["max"]) ? (int)$params["max"] : $min;
	$value = isset($params["value"]) ? (int)$params["value"] : $min;
	
	if($min > $max){
		return;
	}
	
	if(isset($params["min"])){
		unset($params["min"]);
	}
	if(isset($params["max"])){
		unset($params["max"]);
	}
	if(isset($params["value"])){
		unset($params["value"]);
	}
	
	$widget = "<select ";
	
	foreach($params as $attr => $v){
		$widget .= $attr.'="'.$v.'" ';
	}
	
	$widget .= '>';
	
	for(;$min <= $max; $min++){
		if($value == $min){
			$widget .= '<option value="'.$min.'" selected="selected">'.$min.'</option>';
		}
		else{
			$widget .= '<option value="'.$min.'">'.$min.'</option>';
		}
	}
	
	$widget .= "</select>";
	
	echo $widget;
}

?>