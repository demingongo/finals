<?php

namespace Rgs\AdminModule\Util\ContentManager\Tools;

use Novice\Form\Field\Field;

class EditButton extends ToolButton
{
    protected $type;
    protected $itemAction;
    protected $icon;

    public function __construct(){
        parent::__construct([
            'type' => 'primary',
            'item_action' => true,
            'value' => 'edit',
            'label' => 'Edit',
            'icon' => 'fa fa-edit'
        ]);
    }
}