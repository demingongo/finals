<?php

namespace Novice\Form\Field;

class FieldView
{
    private $field;
    private $rendered = false;

    public function __construct(Field $field)
    {
		$this->field = $field;
    }

    public function isRendered()
	{
	  return $this->rendered;
	}

	public function setRendered()
	{
        $this->rendered = true;

        return $this;
	}

	public function render()
	{	
	  if($this->isRendered()){
		  return null;
	  }
	  $this->setRendered();
	  return $field->buildWidget();
	}

	public function setWarningMessage($message)
    {
        $this->field->setWarningMessage($message);
    }

    public function setInfoMessage($message)
    {
        $this->field->setInfoMessage($message);
    }
}
