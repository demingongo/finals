<?php

namespace Rgs\AdminModule\Util;

class StatesManager extends ContentManager {

    private $utils;

    public function __construct($container){
        parent::__construct($container);
        $this->utils = new AdminModuleUtils();
    }

    public function getTitle(){
        return 'layout.states';
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
        return 'RgsCatalogModule:State';
    }

    public function getAddRouteId(){
        return 'rgs_admin_states_add';
    }

    public function getEditRouteId(){
        return 'rgs_admin_states_edit_2';
    }

    public function getColumns(){
        return $this->utils->getStatesColumns();
    }
}