<?php

namespace Novice\Module\ContentManagerModule\Util;

use Novice\Form\Field\Field;

class ToolButtonsGroup
{
    private $toolButtons;

    public function __construct(array $toolButtons = array()){
        $this->toolButtons = [];
        foreach($toolButtons as $tb){
            $this->add($tb);
        }
    }

    public function add(ToolButton $tb){
        array_push($this->toolButtons, $tb);
        return $this;
    }

    public function getToolButtons(){
        return $this->toolButtons;
    }
}