<?php
/**
 * Created by PhpStorm.
 * User: tommy
 * Date: 22-04-2019
 * Time: 11:45
 */

namespace App\Controllers;


use Slim\Http\Request;
use Slim\Http\Response;

class UserController extends Controller
{
	public function __construct($container)
	{
		parent::__construct($container);
	}

	public function list(Request $request, Response $response){
		$auth_id = $this->auth->user()->getId();
		$name = $request->getQueryParam('name');
		$list_id = $request->getQueryParam('listid');
		$usersStore = new \App\Stores\UsersStore($this->sqlManager);

		$users = $usersStore->list(['name'=>$name], 'json');
		$usersFromList = $usersStore->byListId($list_id, 'json');
		unset($users[$auth_id]);
		foreach($users as $key => $value){
			if(isset($usersFromList[$key])){
				unset($users[$key]);
			}
		}
		return $response->withJson($users);
	}
}