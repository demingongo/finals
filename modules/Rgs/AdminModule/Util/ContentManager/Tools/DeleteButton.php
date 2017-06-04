<?php

namespace Rgs\AdminModule\Util\ContentManager\Tools;

use Novice\Form\Field\Field;
use Novice\Module\ContentManagerModule\Util\ToolButton;

class DeleteButton extends ToolButton
{
    protected $type;
    protected $itemAction;
    protected $icon;

    public function __construct(){
        parent::__construct([
            'type' => 'danger',
            'item_action' => true,
            'value' => 'delete',
            'label' => 'Delete',
            'icon' => 'fa fa-trash',
            'attributes' => [
                'data-novice-toggle' => "confirm", 
			    'data-novice-text' => "Delete selected items ?
Warning: This action cannot be undone."
            ]
        ]);
    }
}