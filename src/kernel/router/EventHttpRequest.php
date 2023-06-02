<?php
namespace App\Kernel\Router;

final class EventHttpRequest
{
  /**
   * URL
   *
   * @return string
   */
  public static function getBaseURL(): string
  {
    return sprintf(
      "%s://%s%s",
      isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
      $_SERVER['HTTP_HOST'],
      ''
    );
  }

  /**
   * URI
   *
   * @return string
   */
  public static function getUri(): string
  {
    return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
  }
}