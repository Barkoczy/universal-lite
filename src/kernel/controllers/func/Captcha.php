<?php
namespace App\Kernel\Controllers\Func;

use App\Kernel\Controllers\Func\BaseDataFunction;

final class Captcha extends BaseDataFunction
{
  protected $code;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->code = rand(1111,9999);
  }

  /**
   *
   * @return array
   */
  public function get(): array
  {
    return [
      'code' => $this->code,
      'codes' => str_split($this->code)
    ];
  }
}