<?php
declare(strict_types=1);

namespace App\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class PermissionMiddleware
{
  public function __invoke(Request $request, RequestHandler $handler)
  {
    // @do permission logic...
    
    // @return
    return $handler->handle($request);
  }
}