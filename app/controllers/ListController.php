<?php

namespace App\Controllers;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Interfaces\Controller as iController;

class ListController extends Controller implements iController
{
	public function __construct($container)
	{
		parent::__construct($container);
		if(!$this->auth->check())
		{
			header('Location: /');
			die();
		}
	}

	public function index(Request $request, Response $response){

	}

	public function create(Request $request, Response $response){
		$post = $request->getParsedBody();
		$title = $post['title'];

		if($title == '' || empty($title))
			return $response->withStatus(422)->withJson(['response' => 'error', 'message' => 'Title cannot be empty']);

		$user = $this->auth->user();
		$list = new \App\Models\ListModel();
		$list
			->setName($title)
			->setUserId($user->getId());

		$createBuilder = new \Simplon\Mysql\QueryBuilder\CreateQueryBuilder();

		$listStore = new \App\Stores\ListsStore($this->sqlManager);

		$list = $listStore->create(
			$createBuilder->setModel($list)
		);
		return $this->view->render($response, 'list/list.html.twig', ['list' => $list]);
	}

	public function delete(Request $request, Response $response){

		$list_id = $request->getParam('item_id');

		$listsStore = new \App\Stores\ListsStore($this->sqlManager);
		$list = $listsStore->listFromListId($list_id);
		if($list->getUserId() != $this->auth->user()->getId())
			return $response->withStatus(422)->withJson(['response' => 'error', 'message' => 'You cannot delete this list']);
		$list = $listsStore->delete(
			(new \Simplon\Mysql\QueryBuilder\DeleteQueryBuilder())->setModel($list)->addCondition(\App\Models\ListModel::COLUMN_ID, $list->getId())
		);
		return json_encode(['status' => 'done']);
	}

	public function list(Request $request, Response $response){
		$store = new \App\Stores\ListsStore($this->sqlManager);
		$user = $this->auth->user();
		$lists = $store->listsFromUserId($user->getId());

		return $this->view->render($response, 'lists.html.twig', ['lists'=>$lists]);
	}
}