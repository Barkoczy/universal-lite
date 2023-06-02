<?php
namespace App\Kernel\Router\Schema;

use App\Kernel\Router\Schema\Schema;
use App\Kernel\Router\Enum\RouteObject;
use App\Kernel\Exceptions\MissingParameter;

final class Page extends Schema
{
  /**
   * Initialize
   *
   * @param array $route
   */
  public function __construct(array $route = [])
  {
    parent::__construct($route);
  }
  
  /**
   * Template
   *
   * @param array $route
   * @return void
   */
  public function setTemplate(array $route = []): void
  {
    if (!isset($route[RouteObject::template]))
      throw new MissingParameter('Missing parameter template in routes configuration file');

    $this->template = '@theme/'.$route[RouteObject::template];
  }
}