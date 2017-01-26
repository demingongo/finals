<?php

namespace Novice;

/**
 * Class to store and retrieve the version of Novice
 */
class Version
{
    /**
     * Current Doctrine Version
     */
    const VERSION = '0.1';

    /**
	 *
	 * Compare une version avec celle-ci
	 *
     * @return int retourne -1 si la premi�re version est inf�rieure � la seconde, 
	 *						0 si elles sont �gales,
	 *						1 si la seconde est inf�rieure � la premi�re.
     */
    public static function compare($version)
    {
        $currentVersion = str_replace(' ', '', strtolower(self::VERSION));
        $version        = str_replace(' ', '', $version);

        return version_compare($version, $currentVersion);
    }
}
