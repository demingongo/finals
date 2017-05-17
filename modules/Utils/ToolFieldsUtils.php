<?php

namespace Utils;

use Novice\Form\Field\SelectField;
use Novice\Form\Field\InputField;

class ToolFieldsUtils {

    public function createLimitField(){
        return new SelectField(array(
			'name' => 'limit',
			'empty_option' => false,
			//'bootstrap' => false,
			'options' => array( 
				2 => '2',
				5 => '5',
				10 => '10',
				15 => '15',
				20 => '20',
				25 => '25',
				30 => '30',
				50 => '50'),
			'feedback' => false,
			'attributes' => array(
			'style' => 'width: 99%',
			'data-placeholder' => 'Number per page',
			'data-allow-clear' => 'false',
			'data-minimum-results-for-search' => 'Infinity',
			'class' => 'select2',
			'onchange' => 'this.form.submit()',
			),
		));
    }

    public function createOrderField(array $options){
        return new SelectField(array(
			'name' => 'ordering',
			'empty_option' => false,
			'options' => $options,
			'feedback' => false,
			'attributes' => array(
			'style' => 'width: 99%',
			'data-placeholder' => 'Order by',
			'data-allow-clear' => 'false',
			'data-minimum-results-for-search' => 'Infinity',
			'class' => 'select2',
			'onchange' => 'this.form.submit()',
			),
		));
    }

    public function createSearchField($placeholder = 'Search'){
        return new InputField(array(
			'name' => 'search',
			'placeholder' => $placeholder,
			'feedback' => false,
		));
    }

}