<?php

namespace Rgs\AdminModule\Util;

class StatesManager extends ContentManager {

    private $utils;

    public function __construct($container){
        parent::__construct($container);
        $this->utils = new AdminModuleUtils();
    }

    public function getTitle(){
        return 'Status';
    }

    public function getDefaultOrder(){
      return  'e.name ASC';
    }

    public function getOrderOptions(){
        return array( 
				"e.name ASC" => "Title ascending",
				"e.name DESC" => "Title descending",
				"e.id ASC" => "Id ascending",
				"e.id DESC" => "Id descending",
			);
    }

    public function getVisibilityKey(){
        return 'e.published';
    }

    public function getRepositoryName(){
        return 'RgsCatalogModule:Etat';
    }

    public function getAddRouteId(){
        return 'rgs_admin_etats_add';
    }

    public function getEditRouteId(){
        return 'rgs_admin_etats_edit_2';
    }

    public function getColumns(){
        return $this->utils->getStatesColumns();
    }
}