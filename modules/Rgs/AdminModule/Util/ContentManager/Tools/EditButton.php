<?php

namespace Rgs\AdminModule\Util\ContentManager\Tools;

use Novice\Form\Field\Field;
use Novice\Module\ContentManagerModule\Util\ToolButton;

use Symfony\Component\HttpFoundation\RedirectResponse;

class EditButton extends ToolButton
{
    public function __construct($cm){
        parent::__construct([
            'type' => 'primary',
            'item_action' => true,
            'value' => 'edit',
            'label' => 'Edit',
            'icon' => 'fa fa-edit'
        ], $cm);
    }

    public function onSubmit($ids = null){
        $cm = $this->contentManager;
        $container = $cm->getContainer();
        return new RedirectResponse( $container->get('router')->generate( $cm->getEditRouteId(), ['id'=>$ids[0]] ) );
    }
}