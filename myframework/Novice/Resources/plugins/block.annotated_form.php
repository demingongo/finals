<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     block.annotated_form.php
 * Type:     block
 * Name:     annotated_form
 * Purpose:  build an HTML form a class with annotation form and fields
 * -------------------------------------------------------------
 */
use Novice\Form\FormView;
use Novice\Form\Validator\Validator;
use Novice\Form\Field\Field;
use Novice\Form\Form;

function smarty_block_annotated_form($params, $content, \Smarty_Internal_Template $smarty, &$repeat)
{

    if(!$repeat){
        return "</form>";
    }

	if(empty($params['form']))
	{
		try{
			throw new \Exception("[plugin]{annotated_form} No parameter 'form'");
		}
		catch(\Exception $e){
			trigger_error($e->getMessage());
		}
	}

    // closure to get annotation configuration
    $getConfigurations = function(array $annotations, $className)
    {
        $configurations = array();
        foreach ($annotations as $configuration) {
            if ($configuration instanceof $className) {
                if ($configuration->allowArray()) {
                    $configurations['_'.$configuration->getAliasName()][] = $configuration;
                } elseif (!isset($configurations['_'.$configuration->getAliasName()])) {
                    $configurations['_'.$configuration->getAliasName()] = $configuration;
                } else {
                    throw new \LogicException(sprintf('Multiple "%s" annotations are not allowed.', $configuration->getAliasName()));
                }
            }
        }
        return $configurations;
    };

    $getValidators = function(array $annotations)
    {
        $configurations = array();
        foreach ($annotations as $configuration) {
            if ($configuration instanceof Validator) {
                $configurations[] = $configuration;
            }
        }
        return $configurations;
    };

    // Get annotation reader service
    $reader = $smarty->getContainer()->get('framework.cached.annotation_reader');

    // Get the class name of $params['form']
    $className = class_exists('Doctrine\Common\Util\ClassUtils') ? 
        \Doctrine\Common\Util\ClassUtils::getClass($params['form']) : 
        get_class($params['form']);
		
    // Get $params['form'] ReflectionClass then class annotation 
	$object = new \ReflectionClass($className);
    $classConfigurations = $getConfigurations($reader->getClassAnnotations($object), '\Novice\Annotation\Form\Form');
    
    // return if no class annotation '_form'
    if(!isset($classConfigurations['_form'])){
        return;
    }

    // instance Form
    $form = new Form($object->newInstance());
    $form->setName($classConfigurations['_form']->getValue());

    $props = $object->getProperties();

    foreach($props as $p){
        $annotations = $reader->getPropertyAnnotations($p);
        $propsConf = $getConfigurations($annotations, '\Novice\Annotation\Form\Field');
        if(isset($propsConf['_field'])){
            $f = $propsConf["_field"];
            $class = new \ReflectionClass($f->getFieldClass());
            $field = $class->newInstanceArgs(array($f->getArguments()));
            $field->setName($p->getName());
            $field->setValidators($getValidators($annotations));
            $form->add($field);
        }
    }

    dump($form);
    
    unset($params['form']);
    if(empty($params['name'])){
        $params['name'] = $form->getName();
    }

    $formView = $form->createView();

	$strict = isset($params['strict']) ? (bool) $params['strict'] : true;

	$view = '<form';

    foreach ($params as $attr => $v)
	{
		$view .= ' '.$attr.'="'.htmlspecialchars($v).'"';			
	}

    $view .= '>';

	if(!$formView->isRendered() || !$strict){
		if(empty($params['classRow'])){
			$params['classRow'] = 'form-group has-feedback col-xs-offset-0';
		}
		else{
			$params['classRow'] = 'form-group '.$params['classRow'];
		}
	
		// generate fields one by one.
		foreach ($formView->fields as $field)
		{
			$view .= '<div class="'.htmlspecialchars($params['classRow']).'">'.$field->setRendered()->buildWidget().'</div>';			
		}
		if(!empty($params['_csrf_token']))
		{
			$csrf_token = htmlspecialchars($params['_csrf_token']);
			$view .= '<input type="hidden" id="_csrf_token" name="_csrf_token" value="'.$csrf_token.'" />';
		}
	}


	return $view;

}
?>