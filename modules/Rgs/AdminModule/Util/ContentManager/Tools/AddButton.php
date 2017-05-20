<?php

namespace Rgs\AdminModule\Util\ContentManager\Tools;

use Novice\Form\Field\Field;

class AddButton extends ToolButton
{
    protected $type;
    protected $itemAction;
    protected $icon;

    public function __construct(){
        parent::__construct([
            'type' => 'success',
            'value' => 'add.new',
            'label' => 'Add',
            'icon' => 'fa fa-plus-circle'
        ]);
    }
}