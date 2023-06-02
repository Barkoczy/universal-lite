<?php
namespace App\Kernel\Filesystem;

use App\Kernel\Filesystem\Enum\Directory;

final class Folder
{
  /**
   * Root path
   *
   * @return string
   */
  public static function getRootPath(): string
  {
    return realpath(dirname(__DIR__).'/../../');
  }

  /**
   * App path
   *
   * @return string
   */
  public static function getAppPath(): string
  {
    return realpath(self::getRootPath().'/'.Directory::SRC.'/'.Directory::APP);
  }

  /**
   * App themes path
   *
   * @return string
   */
  public static function getAppThemesPath(): string
  {
    return realpath(self::getAppPath().'/'.Directory::THEMES);
  }

  /**
   * Config path
   *
   * @return string
   */
  public static function getConfigPath(): string
  {
    return realpath(self::getRootPath().'/'.Directory::SRC.'/'.Directory::CONF);
  }

  /**
   * Web path
   *
   * @return string
   */
  public static function getWebPath(): string
  {
    return realpath(self::getRootPath().'/web');
  }

  /**
   * Config path
   *
   * @return string
   */
  public static function getAssetsPath(): string
  {
    return realpath(self::getWebPath().'/'.Directory::ASSETS);
  }

  /**
   * Kernel path
   *
   * @return string
   */
  public static function getKernelPath(): string
  {
    return realpath(self::getRootPath().'/'.Directory::SRC.'/'.Directory::KERNEL);
  }

  /**
   * Console path
   *
   * @return string
   */
  public static function getConsolePath(): string
  {
    return realpath(self::getKernelPath().'/'.Directory::CONSOLE);
  }

  /**
   * Console templates path
   *
   * @return string
   */
  public static function getConsoleTemplatesPath(): string
  {
    return realpath(self::getConsolePath().'/'.Directory::TEMPLATES);
  }

  /**
   * Router path
   *
   * @return string
   */
  public static function getHttpDLConfPath(): string
  {
    return realpath(self::getKernelPath().'/'.Directory::HTTP_DL.'/'.Directory::CONF);
  }


  /**
   * Router path
   *
   * @return string
   */
  public static function getRouterPath(): string
  {
    return realpath(self::getKernelPath().'/'.Directory::ROUTER);
  }

  /**
   * Router config path
   *
   * @return string
   */
  public static function getRouterConfPath(): string
  {
    return realpath(self::getRouterPath().'/'.Directory::CONF);
  }

  /**
   * Controllers path
   *
   * @return string
   */
  public static function getControllersPath(): string
  {
    return realpath(self::getKernelPath().'/'.Directory::CONTROLLERS);
  }

  /**
   * Controllers config path
   *
   * @return string
   */
  public static function getControllersConfigPath(): string
  {
    return realpath(self::getControllersPath().'/'.Directory::CONF);
  }
}