<?php

namespace Novice\Annotation\Form;

use Novice\Annotation\ConfigurationAnnotation;

/**
 * Annotation class for @Extension().
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 */
class Extension extends ConfigurationAnnotation
{
    private $provider;
    private $isService;

    public function setValue($value)
    {
        $this->setProvider($value);
    }

    public function getValue()
    {
         return $this->getProvider();
    }

    public function setProvider($provider){
        $this->provider = $provider;
        $this->isService = false;
    }

    public function getProvider(){
        return $this->provider;
    }

    public function setProviderService($provider){
        $this->provider = $provider;
        $this->isService = true;
    }

    public function isService(){
        return $this->isService;
    }

    public function getAliasName()
    {
        return 'extension';
    }

    public function allowArray()
    {
        return false;
    }
}