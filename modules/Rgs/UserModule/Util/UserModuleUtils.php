<?php

namespace Rgs\UserModule\Util;

use Novice\Form\Form,
	Novice\Password;

use Symfony\Component\Security\Core\Util\StringUtils,
	Symfony\Component\Security\Core\Util\SecureRandomInterface;

class UserModuleUtils
{
	public function compareFormFields(Form $form, $fieldName1, $fieldName2)
	{
		if( !($retour = StringUtils::equals($form->getField($fieldName1)->value(), $form->getField($fieldName2)->value())) ){
			$form->getField($fieldName1)->setWarningMessage();
			$form->getField($fieldName2)->setWarningMessage();
		}

		return $retour;
	}
	
	public function createRandomToken(SecureRandomInterface $generator)
	{
		return bin2hex($generator->nextBytes(32));
	}
}