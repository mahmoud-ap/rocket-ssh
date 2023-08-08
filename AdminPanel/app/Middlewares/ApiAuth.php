<?php

namespace App\Middlewares;

class ApiAuth
{

    private $cont;

    function __construct($cont)
    {
        $this->cont = $cont;
    }

    private $excludeListStatic = array();

    private $excludeListPattern = array();

    private function excludeCheck($uri)
    {
        if (in_array($uri, $this->excludeListStatic)) {
            return true;
        }

        foreach ($this->excludeListPattern as $pattern) {
            if (preg_match("|^" . $pattern . "|is", $uri)) {
                return true;
            }
        }
        return false;
    }

    public function __invoke($request, $response, $next)
    {
        $uri = $request->getUri()->getPath();
        $uri = ltrim($uri, '/');

        $pass = false;
        if ($this->excludeCheck($uri)) {
            $pass = true;
        } else {
        }
        if ($pass) {
            $response = $next($request, $response);
            return $response;
        }

        return $response->withStatus(401);
    }
}
