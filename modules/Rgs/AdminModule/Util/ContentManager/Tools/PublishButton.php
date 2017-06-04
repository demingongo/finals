<?php

namespace Rgs\AdminModule\Util\ContentManager\Tools;

use Novice\Form\Field\Field;
use Novice\Module\ContentManagerModule\Util\ToolButton;

class PublishButton extends ToolButton
{
    protected $type;
    protected $itemAction;
    protected $icon;

    public function __construct(){
        parent::__construct([
            'item_action' => true,
            'value' => 'publish',
            'label' => 'Publish',
            'icon' => 'fa fa-check-circle text-success'
        ]);
    }
}