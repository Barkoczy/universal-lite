<?php
declare(strict_types=1);

namespace App\Kernel;

use App\Kernel\Filesystem\Folder;
use App\Kernel\Filesystem\File;

final class Environment
{
  /**
   * Exists config file 
   *
   * @return boolean
   */
  public static function hasConfigFile(): bool
  {
    return File::exists(Folder::getRootPath().'/.env');
  }

  /**
   * Environment variable
   *
   * @param string $key
   * @param string $default
   * @return string
   */
  public static function var(string $key = '', string $default = ''): string
  {
    // @value
    if (false !== getenv($key))
      return getenv($key);
    if (isset($_SERVER[$key]))
      return $_SERVER[$key];

    // @default
    return 0 === strlen($default) ? '' : $default;
  }
}