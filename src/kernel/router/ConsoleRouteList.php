<?php
namespace App\Kernel\Router;

use Symfony\Component\Yaml\Yaml;
use App\Kernel\Filesystem\Folder;
use App\Kernel\Router\Enum\RouteObject;
use App\Kernel\Router\EventHttpRequest;
use App\Kernel\Router\RouteList;
use App\Kernel\Exceptions\CannotReadFileFromFilesource;

final class ConsoleRouteList extends RouteList
{
  /**
   * Constructor
   */
  public function __construct()
  {
    // @confPath
    $confPath = Folder::getRouterConfPath().'/console.yml';

    // @validate
    if (!is_file($confPath))
      throw new CannotReadFileFromFilesource('Cannot read kernel console yaml configuration file due to insufficient permissions');

    // @conf
		$conf = Yaml::parseFile($confPath)??[];

    // @parent
    parent::__construct($conf);
  }

  /**
   * Return route object by url
   *
   * @return array
   */
  public function findByURL(): array
  {
    return $this->find(RouteObject::url, EventHttpRequest::getUri());
  }

  /**
   * Return route object by controller
   *
   * @param string $controller
   * @return array
   */
  public function findByController(string $controller = ''): array
  {
    return $this->find(RouteObject::controller, $controller);
  }
}