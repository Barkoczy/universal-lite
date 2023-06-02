<?php
declare(strict_types=1);

namespace App\Kernel\Filesystem;

final class File
{
  /**
   * File exists
   *
   * @param string $filepath
   * @return boolean
   */
  public static function exists(string $filepath = ''): bool
  {
    return is_file($filepath);
  }
}