<?php

namespace App\Middlewares;

class OptionsMethodCheck
{

  public function __invoke($request, $response, $next)
  {
    if ($request->isOptions())
    {
      return $response->withStatus(204);
    }

    $response = $next($request, $response);
    return $response;
  }
}
