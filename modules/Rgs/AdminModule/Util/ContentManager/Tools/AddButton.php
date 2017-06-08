<?php

namespace Rgs\AdminModule\Util\ContentManager\Tools;

use Novice\Form\Field\Field;
use Novice\Module\ContentManagerModule\Util\ToolButton;

use Symfony\Component\HttpFoundation\RedirectResponse;

class AddButton extends ToolButton
{
    public function __construct($cm){
        parent::__construct($cm, [
            'type' => 'success',
            'value' => 'add.new',
            'label' => 'Add',
            'icon' => 'fa fa-plus-circle'
        ]);
    }

    public function onSubmit(){
        $cm = $this->contentManager;
        $container = $cm->getContainer();
        
        return new RedirectResponse( $container->get('router')->generate( $cm->getAddRouteId() ) );
    }
}