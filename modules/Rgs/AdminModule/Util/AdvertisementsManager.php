<?php

namespace Rgs\AdminModule\Util;

class AdvertisementsManager extends ContentManager {

    private $utils;

    public function __construct($container){
        parent::__construct($container);
        $this->utils = new AdminModuleUtils();
    }

    public function getTitle(){
        return 'Advertisements';
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
        return 'RgsCatalogModule:Advertisement';
    }

    public function getAddRouteId(){
        return 'rgs_admin_advertisements_add';
    }

    public function getEditRouteId(){
        return 'rgs_admin_advertisements_edit_2';
    }

    public function getColumns(){
        return $this->utils->getAdvertisementsColumns();
    }
}