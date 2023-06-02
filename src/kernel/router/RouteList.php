<?php
namespace App\Kernel\Router;

abstract class RouteList
{
  public $conf;

  /**
   * Constructor
   * 
   * @param array $conf
   */
  public function __construct(array $conf = []) {
    $this->conf = $conf;
  }

  abstract public function findByURL(): array;
  abstract public function findByController(string $controller = ''): array;

  /**
   * Find key in configuration file
   *
   * @param string $key
   * @param string $value
   * @return mixed
   */
  public function find(string $key = '', string $value = ''): mixed
  {
    // @not-exists
    if (!is_array($this->conf) || 0 === count($this->conf))
      return [];

    // @index
    $index = array_search($value, array_column(
      $this->conf, $key
    ));

    // @not-found
    if (false === $index)
      return [];

    // @return
    return $this->conf[$index];
  }
}