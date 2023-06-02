<?php
namespace App\Kernel\Router\Entity;

use Symfony\Component\Yaml\Yaml;
use App\Kernel\Filesystem\Folder;
use App\Kernel\Router\Enum\Method;
use App\Kernel\Router\Enum\RouteObject;
use App\Kernel\Router\Enum\Controllers;
use App\Kernel\Exceptions\CannotReadFileFromFilesource;
use App\Kernel\Exceptions\CannotFindFileSpecified;
use App\Kernel\Exceptions\MissingParameter;

abstract class Entity
{
  public $method;
  public $url;
  public $controller;
  public $template;
  public $data;

  /**
   *
   * @param string $class
   */
  public function __construct(string $class = '')
  {
    // @validate
    if (!is_file($this->filepath()))
      throw new CannotReadFileFromFilesource('Cannot read routes file due to insufficient permissions');

    // @conf
		$conf = Yaml::parseFile($this->filepath());

    // @validate
    if (!isset($conf[$class]))
      throw new CannotFindFileSpecified('Entity '.$class.' not found in entity configuration file');

    // @route
    $route = $conf[$class];

    // @set
    $this->setMethod($route);
    $this->setURL($route);
    $this->setController($route);
    $this->setTemplate($route);
    $this->setData($route);
  }

  /**
   * Return config file path
   *
   * @return string
   */
  private function filepath(): string
  {
    return Folder::getRouterConfPath().'/entity.yml';
  }

  /**
   * Http Request Method
   *
   * @param array $route
   * @return void
   */
  private function setMethod(array $route = []): void
  {
    if (isset($route[RouteObject::method]) &&
      in_array($route[RouteObject::method],[Method::GET,Method::POST])
    ) {
      $this->method = $route[RouteObject::method];
    } else  {
      $this->method = Method::GET;
    }
  }

  /**
   * @param array $route
   * @return void
   */
  private function setURL(array $route = []): void
  {
    if (!isset($route[RouteObject::url]))
      throw new MissingParameter('Missing parameter url in routes configuration file');

    $this->url = $route[RouteObject::url];
  }

  /**
   * @param array $route
   * @return void
   */
  private function setController(array $route = []): void
  {
    if(isset($route[RouteObject::controller]) &&
      0 < strlen($route[RouteObject::controller])
    ) {
      $this->controller = $route[RouteObject::controller];
    } else {
      $this->controller = Controllers::base;
    }
  }

  /**
   * @param array $route
   * @return void
   */
  private function setTemplate(array $route = []): void
  {
    if (!isset($route[RouteObject::template]))
      throw new MissingParameter('Missing parameter template in routes configuration file');

    $this->template = '@console/'.$route[RouteObject::template];
  }

  /**
   * @param array $route
   * @return void
   */
  private function setData(array $route = []): void
  {
    if(isset($route[RouteObject::data]) &&
      is_array($route[RouteObject::data]) &&
      0 < count($route[RouteObject::data])
    ) {
      $this->data = $route[RouteObject::data];
    } else {
      $this->data = [];
    }
  }
}