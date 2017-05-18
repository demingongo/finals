<?php

namespace Rgs\AdminModule\Util;

abstract class ContentManager {

    protected $service_container;

    public function __construct($service_container){
        $this->$service_container = $service_container;
    }

    public function getName(){
        return '';
    }

    public function getTitle(){
        return '';
    }

    public function getCustomFields(){
        return array();
    }

    public function processCustomFields($request, array $where, $customFields){
        return;
    }

    public function getColumns(){
        return array();
    }

    abstract public function getDefaultOrder();

    abstract public function getOrderOptions();

    abstract public function getVisibilityKey();

    abstract public function getRepositoryName();

    abstract public function getAddRouteId();

    abstract public function getEditRouteId();
}