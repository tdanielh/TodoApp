<?php
/**
 * Created by PhpStorm.
 * User: tommy
 * Date: 29-11-2018
 * Time: 14:33
 */

namespace App\Interfaces;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

interface Controller
{
	public function index(Request $request, Response $response);
	public function list(Request $request, Response $response);
	public function create(Request $request, Response $response);
	public function delete(Request $request, Response $response);
}