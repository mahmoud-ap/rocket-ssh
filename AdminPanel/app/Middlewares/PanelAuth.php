<?php

namespace App\Middlewares;

class PanelAuth
{

  private $cont;

  function __construct($cont)
  {
    $this->cont = $cont;
  }

  private $excludeListStatic = array(
    '/login',
    '/ajax/login',
  );

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
      $userId = session()->get("admin_login");
      if ($userId) {
        $aModal = new \App\Models\Admins();
        $userInfo = $aModal->getInfo($userId);
        if ($userInfo->is_active) {
          $request = $request->withAttribute('uid', $userId);
          $request = $request->withAttribute('userInfo', $userInfo);
          $this->cont["userInfo"] = $userInfo;
          session()->set("userInfo", $userInfo);
          $pass = true;
        } else {
          session()->delete('admin_login');
        }
      }
    }
    if ($pass) {
      $response = $next($request, $response);
      return $response;
    }
    return $response->withStatus(302)->withHeader("location", baseUrl("/login"));
  }
}
