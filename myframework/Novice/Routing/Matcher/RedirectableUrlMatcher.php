<?php

namespace Novice\Routing\Matcher;

use Symfony\Component\Routing\Matcher\RedirectableUrlMatcher as RUM;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Route;


class RedirectableUrlMatcher extends RUM
{
	/**
     * Redirects the user to another URL.
     *
     * @param string      $path   The path info to redirect to.
     * @param string      $route  The route name that matched
     * @param string|null $scheme The URL scheme (null to keep the current one)
     *
     * @return array An array of parameters
     *
     * @api
     */
    public function redirect($path, $route, $scheme = null){
		return array(
            '_controller' => 'FrameworkModule:RedirectController:urlRedirect',
            'path'        => $path,
            'permanent'   => true,
            'scheme'      => $scheme,
            'httpPort'    => $this->context->getHttpPort(),
            'httpsPort'   => $this->context->getHttpsPort(),
            '_route'      => $route,
        );
		dump($path);
		dump($route);
		dump($scheme);
		exit(__METHOD__);
	}
}
