<?php

namespace Rgs\AdminModule\Util;

class BrandsManager extends ContentManager {

    private $utils;

    public function __construct($container){
        parent::__construct($container);
        $this->utils = new AdminModuleUtils();
    }

    public function getTitle(){
        return 'layout.brands';
    }

    public function getDefaultOrder(){
      return  'm.name ASC';
    }

    public function getOrderOptions(){
        return array( 
				"m.name ASC" => "Title ascending",
				"m.name DESC" => "Title descending",
				"m.id ASC" => "Id ascending",
				"m.id DESC" => "Id descending",
			);
    }

    public function getVisibilityKey(){
        return 'm.published';
    }

    public function getEntityName(){
        return 'RgsCatalogModule:Brand';
    }

    public function getAlias(){
        return 'm';
    }

    public function getAddRouteId(){
        return 'rgs_admin_brands_add';
    }

    public function getEditRouteId(){
        return 'rgs_admin_brands_edit_2';
    }

    public function getColumns(){
        return $this->utils->getBrandsColumns();
    }
}