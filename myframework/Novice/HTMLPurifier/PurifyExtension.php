<?php

namespace Novice\HTMLPurifier;

use Novice\Templating\Extension\ModifierExtension;

class PurifyExtension extends ModifierExtension
{
    private $purifier;

    public function __construct(\HTMLPurifier $purifier)
    {
        $this->purifier = $purifier;
    }

	public function execute($string, $length = 80)
	{
		return $this->purifier->purify($string);
	}

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'purify';
    }


	/*public function purify($string)
	{
		return $this->purifier->purify($string);
	}*/
	/**
     * {@inheritDoc}
     */
    /*public function getCallback()
    {
        return array($this, 'purify');
    }*/
}
