<?php
/**
 * Smarty plugin
 */

/**
 * Smarty {notification} function plugin
 * Type:     function<br>
 * Name:     notification<br>
 * Purpose:  notification in template
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
 * @return string
 */

function smarty_function_notification($params, &$smarty)
{

	$message = $params["message"];
	$class='';
	$type='info';
	$hint='info';
	$icon='info-sign';

	$retour = '';

	if (!empty($params['class'])) {
        $class = $params['class'];
	}

	if (!empty($params['type'])) {
		$params['type'] = strtolower($params['type']);
		switch($params['type']){
			//case 'info':
			case 'success':
				$type = $params['type']; $hint = $params['type'];
				$icon='ok-sign';
			break;
			case 'warning':
				$type = $params['type']; $hint = $params['type'];
				$icon='alert';
			break;
			case 'danger':
			case 'error':
				$type = 'danger'; $hint = 'Error';
				$icon='exclamation-sign';
			break;
		}
	}
	
	if (isset($params['sign']) && is_bool($params['sign']) && $params['sign'] == true) {
        $sign = function($hint) use ($icon){return '
		<span class="glyphicon glyphicon-'.$icon.'" aria-hidden="true"></span>
		<span class="sr-only">'.ucfirst($hint).':</span>';};
	}
	else{
		if (!empty($params['glyphicon']) && is_string($params['glyphicon'])) {
			$sign = function($hint) use ($params){return '
		<span class="glyphicon glyphicon-'.$params['glyphicon'].'" aria-hidden="true"></span>
		<span class="sr-only">'.ucfirst($hint).':</span>';};
		}
		else{
			$sign = function($hint){return '';};
		}
	}

	if (isset($params['close']) && is_bool($params['close']) && !$params['close']) {
        $close = '';
	}
	else{
		$close = '<button type="button" class="close" data-dismiss="alert" aria-label="close">&times;</button>';
	}

	if(is_array($message)){
		$retour .= '<div class="col-lg-12">';
		foreach($message as $mess){
			$retour .= '
<div class="alert alert-dismissable alert-'.$type.' col-sm-offset-1 col-sm-10 text-center '.$class.'" role="alert">
	'.$close.'
'.$sign($hint).'
	'.$mess.'
</div>
';
		}
		$retour .= '</div>';
	}
	else{
	$retour = '
<div class="col-lg-12">
<div class="alert alert-dismissable alert-'.$type.' col-sm-offset-1 col-sm-10 text-center '.$class.'" role="alert">
	'.$close.'
'.$sign($hint).'
	'.$message.'
</div>
</div>';
	}

	return $retour;
}

?>