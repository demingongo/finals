<?php

namespace Novice\Module\SmartyBootstrapModule\Util;

class ItemProperty{

    private $propertyName;

    public function __construct($propertyName){
        $this->propertyName = $propertyName;
    }

    public function getPropertyName(){
        return $this->propertyName;
    }


}