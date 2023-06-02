<?php
namespace App\Kernel\Controllers;

use Symfony\Component\Yaml\Yaml;
use App\Kernel\Filesystem\Folder;
use App\Kernel\Exceptions\CannotReadFileFromFilesource;

final class DataFunctions
{
  private $conf;

  /**
   * Constructor
   */
  public function __construct()
  {
    // @filepath
    $filepath = Folder::getControllersConfigPath().'/functions.yml';

    // @validate
    if (!is_file($filepath))
      throw new CannotReadFileFromFilesource('Cannot read component functions configuration file due to insufficient permissions');

    // @conf
		$this->conf = Yaml::parseFile($filepath);
  }

  /**
   * Exists
   *
   * @param string $func
   * @return boolean
   */
  public function has(string $func = ''): bool
  {
    return isset($this->conf[$func]);
  }

  /**
   * Return function value
   *
   * @param string $func
   * @return mixed
   */
  public function get(string $func = ''): mixed
  {
    return (new $this->conf[$func]())->get();
  }
}