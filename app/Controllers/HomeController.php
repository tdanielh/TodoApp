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

class HomeController extends Controller
{
	public function index(Request $request, Response $response){
		if($this->auth->check()){
			$store = new \App\Stores\ListsStore($this->sqlManager);
			$user = $this->auth->user();
			$lists = $store->listsFromUserId($user->getId());

			return $this->view->render($response, 'lists.html.twig', ['lists'=>$lists]);
		}
		else
			return $this->view->render($response, 'home.html.twig');
	}

	public function login(Request $request, Response $response){
		$email = $request->getParam('email');
		$password = $request->getParam('password');

		if($email == '' || $password == '')
			return $response->withStatus(422)->withJson(['response' => 'error', 'message' => 'Please type both email and password']);
		$auth = $this->auth->attempt($email, $password);
		if(!$auth)
			return $response->withStatus(422)->withJson(['response' => 'error', 'message' => 'Not valid']);
		return $response->withStatus(200)->withJson(['response' => 'success']);
	}

	public function logout(Request $request, Response $response){
		$this->auth->logout();
	}
}