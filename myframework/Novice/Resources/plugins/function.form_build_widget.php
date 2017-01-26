<?php
/**
 * Smarty plugin
 */

/**
 * Smarty {form_build_widget} function plugin
 * Type:     function<br>
 * Name:     path<br>
 * Purpose:  
 *
 * @author   Demingongo-Litemo Stephane
 *
 * @param array                    $params   parameters
 *
 * @return view
 */
use Novice\Form\FormView;
use Novice\Form\Field\Field;

function smarty_function_form_build_widget($params, &$smarty)
{
	if(empty($params['form']))
	{
		try{
			throw new \Exception("[plugin]{form_build_widget} No parameter 'form'");
		}
		catch(\Exception $e){
			trigger_error($e->getMessage());
		}
	}

	if(!$params['form'] instanceof FormView){
			try{
				throw new \Exception("[plugin]{form_build_widget} Parameter 'form' must be instance of 'Novice\Form\FormView'");
			}
			catch(\Exception $e){
				trigger_error($e->getMessage());
				return;
			}
	}

	$strict = isset($params['strict']) ? (bool) $params['strict'] : true;

	$view = '';

	if(!empty($params['field'])){
			if($params['form'][$params['field']]->isRendered() && $strict){
				return;
			}

			if(empty($params['class'])){
				$params['class'] = 'form-group col-xs-offset-0';
			}
			else{
				$params['class'] = 'form-group '.$params['class'];
			}

			$attributes = "";

			if(isset($params['attr']) && is_array($params['attr']) && !empty($params['attr'])){
				/*foreach($params['attr'] as $k => $v)
				{
					if(is_string($k))
					{
						$attributes .= htmlspecialchars($k).'="'.htmlspecialchars($v).'" ';
					}
				}*/
				$params['form'][$params['field']]->setAttributes($params['attr']);
			}

			if(!empty($params['help'])){
				$params['help'] = '<span class="help-block';
				if(!empty($params['errors']) && $params['errors'] == true){
					$params['help'] .= ' with-errors';
				}
				$params['help'] .= '">'.$params['help-with-error'].'</span>';
			}
			else if(!empty($params['help-with-error'])){
				$params['help'] = '<span class="help-block with-errors">'.$params['help-with-error'].'</span>';
			}
		
		try{
			$view .= $params['form'][$params['field']]->setRendered()->buildWidget();
		}
		catch(\Exception $e){
			trigger_error($e->getMessage());
			return;
		}
	}
	else{
		if(!$params['form']->isRendered() || !$strict){
			if(empty($params['class'])){
				$params['class'] = 'row form-group has-feedback col-xs-offset-0';
			}
			else{
				$params['class'] = 'row form-group '.$params['class'];
			}
		
			// On génère un par un les champs du formulaire.
			foreach ($params['form']->fields as $field)
			{
				$view .= '<div class="'.htmlspecialchars($params['class']).'">'.$field->setRendered()->buildWidget().'</div>';			
			}

			if(!empty($params['_csrf_token']))
			{
				$csrf_token = htmlspecialchars($params['_csrf_token']);
				$view .= '<input type="hidden" id="_csrf_token" name="_csrf_token" value="'.$csrf_token.'" />';
			}
		}
	}


	return $view;

}
?>