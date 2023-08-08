<?php

namespace App\Middlewares;

class PanelPerms
{

    private $cont;

    function __construct($container)
    {
        $this->cont = $container;
    }

    private $excludeListStatic = array(
        '/',
    );

    private $excludeListPattern = array();

    private function excludeCheck($uri)
    {
        if (in_array($uri, $this->excludeListStatic)) {
            return true;
        }
        // dynamic rule check
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

            $userInfo     = $request->getAttribute('userInfo');
            $role         = $userInfo->role;
            $notValidUrl = [
                "settings",
                "admins",
            ];
            if ($role != "admin") {
                $passShow = true;
                foreach ($notValidUrl  as  $value) {
                    if (preg_match("|^" . $value . "$|is", $uri)) {
                        $passShow = false;
                    }
                }
                if (!$passShow) {
                    return $response->withStatus(302)->withHeader("location", baseUrl("/dashboard"));
                }
            }
            $pass = true;
        }
        if ($pass) {
            $response = $next($request, $response);
            return $response;
        }
    }
}
