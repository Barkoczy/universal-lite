<?php
namespace App\Kernel\Extensions\Twig;

use App\Kernel\Extensions\Guard;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class Csrf extends AbstractExtension implements GlobalsInterface
{
  /**
   * @var Guard
   */
  protected $csrf;
  
  public function __construct(Guard $csrf)
  {
    $this->csrf = $csrf;
  }

  /**
   * Csrf Global Twig
   *
   * @return array
   */
  public function getGlobals(): array
  {
    // CSRF token name and value
    $csrfNameKey = $this->csrf->getTokenNameKey();
    $csrfValueKey = $this->csrf->getTokenValueKey();
    $csrfName = $this->csrf->getTokenName();
    $csrfValue = $this->csrf->getTokenValue();

    return [
      'csrf'   => [
        'keys' => [
          'name'  => $csrfNameKey,
          'value' => $csrfValueKey
        ],
        'name'  => $csrfName,
        'value' => $csrfValue
      ]
    ];
  }
}