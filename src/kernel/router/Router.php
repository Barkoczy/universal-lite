<?php
namespace App\Kernel\Router;

use App\Kernel\Router\Entity\Welcome;
use App\Kernel\Router\Enum\RouterSchema;
use App\Kernel\Router\PageRouteList;
use App\Kernel\Router\ApiRouteList;
use App\Kernel\Router\ConsoleRouteList;
use App\Kernel\Router\Schema\Api as ApiSchema;
use App\Kernel\Router\Schema\Console as ConsoleSchema;
use App\Kernel\Router\Schema\Page as PageSchema;

final class Router
{
  /**
   * Find route config by URL
   *
   * @return array
   */
  public function findByURL(): array
  {
    // @apiConf
    $apiConf = (new ApiRouteList())->findByURL();

    // @validate
    if([] !== $apiConf)
      return $this->route(RouterSchema::API, $apiConf);

    // @consoleConf
    $consoleConf = (new ConsoleRouteList())->findByURL();

    // @validate
    if([] !== $consoleConf)
      return $this->route(RouterSchema::CONSOLE, $consoleConf);

    // @pageConf
    $pageConf = (new PageRouteList())->findByURL();

    // @validate
    if([] !== $pageConf)
      return $this->route(RouterSchema::PAGE, $pageConf);

    // @default
    return $this->defaultRoute();
  }

  /**
   * Route
   *
   * @param string $schema
   * @param array $conf
   * @return array
   */
  private function route(string $schema = '', array $conf = []): array
  {
    // @api
    if(RouterSchema::API === $schema)
      return (array)(new ApiSchema($conf));

    // @console
    if(RouterSchema::CONSOLE === $schema)
      return (array)(new ConsoleSchema($conf));

    // @page
    if(RouterSchema::PAGE === $schema)
      return (array)(new PageSchema($conf));
  }

  /**
   * Default route
   *
   * @return array
   */
  private function defaultRoute(): array
  {
    return (array)(new Welcome());
  }
}