<?php
/**
 * Created by PhpStorm.
 * User: tommy
 * Date: 29-11-2018
 * Time: 13:56
 */

namespace App\Controllers;


use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class HomeController
{
	protected $container;

	public function __construct($container)
	{
		$this->container = $container;
	}

	public function index(Request $request, Response $response){
		return 'Front page';
	}
}