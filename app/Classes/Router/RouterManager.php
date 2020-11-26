<?php

namespace App\Classes\Router;

/**
 * Class RouterManager
 * @package App\Classes\Router
 */
class RouterManager
{
    /**
     * Method check and get current action
     * @param string $method
     * @param string $requestMethod
     * @return bool
     */
    public function handle(string $method, string $requestMethod)
    {
        $router = config('self_routing');

        if (isset($router[$method]) && $router[$method]['method'] == strtolower($requestMethod)) {
            return $router[$method]['action'];
        }

        return false;
    }
}
