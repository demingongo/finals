<?php

namespace Rgs\AdminModule\Util;

use Rgs\AdminModule\Util\ContentManager\Tools as CMTools;

abstract class ContentManager {

    protected $container;

    public function __construct($container){
        $this->container = $container;
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

    public function getToolsButtons(){
        return [
            new CMTools\AddButton(),
            new CMTools\EditButton(),
            new CMTools\PublishButton(),
            new CMTools\UnpublishButton(),
            new CMTools\DeleteButton()
        ];
    }

    abstract public function getDefaultOrder();

    abstract public function getOrderOptions();

    abstract public function getVisibilityKey();

    abstract public function getRepositoryName();

    abstract public function getAddRouteId();

    abstract public function getEditRouteId();
}