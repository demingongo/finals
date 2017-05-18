<?php

namespace Rgs\AdminModule\Util;

class ArticlesManager extends ContentManager {

    private $utils;

    public function __construct($container){
        parent::__construct($container);
        $this->utils = new AdminModuleUtils();
    }

    public function getTitle(){
        return 'Articles';
    }

    public function getDefaultOrder(){
      return  'a.name ASC';
    }

    public function getOrderOptions(){
        return array( 
				"a.name ASC" => "Title ascending",
				"a.name DESC" => "Title descending",
				"a.id ASC" => "Id ascending",
				"a.id DESC" => "Id descending",
			);
    }

    public function getVisibilityKey(){
        return 'a.published';
    }

    public function getRepositoryName(){
        return 'RgsCatalogModule:Article';
    }

    public function getAddRouteId(){
        return 'rgs_admin_articles_add';
    }

    public function getEditRouteId(){
        return 'rgs_admin_articles_edit_2';
    }

    public function getColumns(){
        return $this->utils->getArticlesColumns();
    }

    public function getCustomFields(){
        $formFieldExtension =  new \Novice\Form\Extension\Entity\EntityExtension($this->container->get('managers'), array(
		'class' => 'RgsCatalogModule:Categorie',
		'choice_label' => function($cat){return $cat->getName();},
		'query_builder' => function ($er) {
				return $er->createQueryBuilder('c')
					->orderBy('c.name', 'ASC');
		},
        'name' => 'categorie',
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

		$categoriesField = $formFieldExtension->createField();
        return [
            'categories' => $categoriesField
        ];
    }

    public function processCustomFields($request, array $where, $customFields){
        $byCategorie = null;
        if($request->request->has('categorie')){
			$req_byCategorie = $request->request->get('categorie');
			if(!empty($req_byCategorie)){
				$byCategorie = $req_byCategorie;
				$where['a.categorie'] = $byCategorie;
			}
		}
        $customFields['categories']->setValue($byCategorie);
        return $where;
    }
}