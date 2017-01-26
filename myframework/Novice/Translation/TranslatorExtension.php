<?php

namespace Novice\Translation;

use Novice\Templating\Extension\ModifierExtension;
use Symfony\Component\Translation\TranslatorInterface;

class TranslatorExtension extends ModifierExtension
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

	public function execute($string, array $parameters = array(), $domain = null, $locale = null)
	{
		return $this->translator->trans($string, $parameters, $domain, $locale);
	}

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'trans';
    }
}
