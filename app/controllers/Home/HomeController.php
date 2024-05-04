<?php
namespace App\Controllers\Home;

use App\Controllers\BaseController;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HomeController extends BaseController
{
	public function __construct(ContainerInterface $container)
	{
		parent::__construct($container);
	}

	public function index(Request $request, Response $response, $args)
	{
		$this->logRequest($request);
		return $response->withJson(['result' => 'Welcome']);
	}
}
