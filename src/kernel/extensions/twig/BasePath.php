<?php
namespace App\Kernel\Extensions\Twig;

use App\Kernel\Router\EventHttpRequest;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BasePath extends AbstractExtension
{
  private $basePath;

  public function __construct()
  {
    $this->basePath = EventHttpRequest::getBaseURL();
  }

  /**
   *
   * @return array
   */
  public function getFunctions(): array
  {
    return [
      new TwigFunction('basePath', function () : string {
        return $this->basePath;
      })
    ];
  }
}