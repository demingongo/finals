<?php

namespace Rgs\AdminModule\Util\ContentManager\Tools;

use Novice\Form\Field\Field;
use Novice\Module\ContentManagerModule\Util\ToolButton;

class UnpublishButton extends ToolButton
{
    protected $type;
    protected $itemAction;
    protected $icon;

    public function __construct(){
        parent::__construct([
            'item_action' => true,
            'value' => 'unpublish',
            'label' => 'Unpublish',
            'icon' => 'fa fa-times-circle text-danger'
        ]);
    }
}