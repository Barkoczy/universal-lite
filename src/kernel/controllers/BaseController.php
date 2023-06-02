<?php
namespace App\Kernel\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Kernel\Controllers\Controller;

class BaseController extends Controller
{
  public function __invoke(Request $request, Response $response, array $args): Response
  {
    // @request
    $this->controller->request($request);

    // @args
    $this->controller->args($args);

    // @response
    return $this->view->render(
      $response, 
      $this->controller->template(), 
      $this->controller->data()
    );
  }
}