<?php

namespace Novice\Module\ContentManagerModule\Util;

use Novice\Form\Field\Field;

interface ContentManagerInterface
{
    public function getContainer();

    public function getName();

    public function getTitle();

    public function getDefaultOrder();

    public function getOrderOptions();

    public function getEntityName();
    
    public function getAlias();

    public function getCustomFields();

    public function processCustomFields($request, array $where, $customFields);

    public function getColumns();

    public function getToolButtonsGroup();
}