<?php

namespace Rgs\AdminModule\Util;

class CategoriesManager extends ContentManager {

    private $utils;

    public function __construct($container){
        parent::__construct($container);
        $this->utils = new AdminModuleUtils();
    }

    public function getName(){
        return 'category';
    }

    public function getTitle(){
        return 'layout.categories';
    }

    public function getDefaultOrder(){
      return  'c.name ASC';
    }

    public function getOrderOptions(){
        return array( 
				"c.name ASC" => "Title ascending",
				"c.name DESC" => "Title descending",
				"c.id ASC" => "Id ascending",
				"c.id DESC" => "Id descending",
			);
    }

    public function getVisibilityKey(){
        return 'c.published';
    }

    public function getRepositoryName(){
        return 'RgsCatalogModule:Category';
    }

    public function getAddRouteId(){
        return 'rgs_admin_categories_add';
    }

    public function getEditRouteId(){
        return 'rgs_admin_categories_edit_2';
    }

    public function getColumns(){
        return $this->utils->getCategoriesColumns();
    }
}