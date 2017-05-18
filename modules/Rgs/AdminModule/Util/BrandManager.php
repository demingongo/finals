<?php

namespace Rgs\AdminModule\Util;

class BrandManager extends ContentManager {

    private $utils;

    public function __construct($container){
        parent::__construct($container);
        $this->utils = new AdminModuleUtils();
    }

    public function getTitle(){
        return 'Brands';
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

    public function getRepositoryName(){
        return 'RgsCatalogModule:Marque';
    }

    public function getAddRouteId(){
        return 'rgs_admin_marques_add';
    }

    public function getEditRouteId(){
        return 'rgs_admin_marques_edit_2';
    }

    public function getColumns(){
        return $this->utils->getBrandsColumns();
    }
}