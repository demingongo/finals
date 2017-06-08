<?php

namespace Utils;

use Novice\Form\Field\SelectField,
	Novice\Form\Field\InputField,
	Novice\Form\Extension\Entity\EntityExtension;

use Novice\Module\ContentManagerModule\Util\FilterFieldsCreator;

class ToolFieldsUtils extends FilterFieldsCreator{

	public function createCategoryEntityField(\DoctrineModule\ManagerRegistry $doctrine){
        $formFieldExtension =  new \Novice\Form\Extension\Entity\EntityExtension($doctrine, array(
		'class' => 'RgsCatalogModule:Category',
		'choice_label' => function($cat){return $cat->getName();},
		'query_builder' => function ($er) {
				return $er->createQueryBuilder('c')
					->orderBy('c.name', 'ASC');
		},
        'name' => 'category',
		'feedback' => false,
		'attributes' => array(
			'style' => 'width: 99%',
			'data-placeholder' => 'All Categories',
			'data-allow-clear' => 'true',
			'data-minimum-results-for-search' => 15,
			'class' => 'select2',
			'onchange' => 'this.form.submit()',
			),
		));

		return $formFieldExtension->createField();
    }

}