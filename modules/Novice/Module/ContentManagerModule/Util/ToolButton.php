<?php

namespace Novice\Module\ContentManagerModule\Util;

use Novice\Form\Field\Field;

class ToolButton extends Field
{
    protected $type;
    protected $item_action;
    protected $icon;

    protected $contentManager;

    public function __construct(ContentManagerInterface $cm, array $options = array()){
        parent::__construct($options);
        $this->contentManager = $cm;
    }

    protected function setType($type)
    {
        $this->type = (string) $type;
    }

    protected function setIcon($icon)
    {
        $this->icon = (string) $icon;
    }

    protected function setItem_action($item_action)
    {
        $this->item_action = (bool) $item_action;
    }

    public function itemAction(){
        return $this->item_action;
    }

    public function buildWidget()
    {
        $widget = '<button type="submit" name="submit[]" class="btn btn-outline btn-'
                    .($this->type ? $this->type : 'default')
                    .($this->item_action ? ' itemAction ': ' ')
                    .'to-xs"';

        if (!empty($this->attributes))
        {
		    foreach($this->attributes as $attr => $val)
			{
				$widget .= ' '.$attr.'="'.$val.'"';
			}
        }

        if (!empty($this->value))
        {
			$widget .= ' value="'.htmlspecialchars($this->value).'"';
        }

        $widget .= '>';

        if(!empty($this->icon)){
            $widget .= '<span class="'.htmlspecialchars($this->icon).'"></span> ';
        }
        $widget .= ($this->label ? $this->label : '').'</button>';

        return $widget;
    }

    public function onSubmit(){
    }
}