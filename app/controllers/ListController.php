<?php

namespace App\Controllers;

use App\traits\Shareable;
use function DusanKasan\Knapsack\dump;
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
		$gotolist = isset($post['gotolist']);
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
		if($gotolist){
			return $response->withStatus(200)->withJson(['gotolist' => $gotolist, 'listId' => $list->getId()]);
		}

		return $this->view->render($response, 'list/list.html.twig', ['list' => $list]);
	}

	public function delete(Request $request, Response $response){
		$list_id = $request->getParam('item_id');
		$listsStore = new \App\Stores\ListsStore($this->sqlManager);
		$list = $listsStore->byListId($list_id);
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

		$selected = $request->getParam('selected');

		$lists = $store->listsByUserId($user->getId(),$selected);
		return $this->view->render($response, 'lists.html.twig', ['lists'=>$lists]);
	}

	public function search(Request $request, Response $response){
		$search = [];
		return $this->list($request, $response, $search);
	}

	public function sharedWith(Request $request, Response $response){
		$usersStore = new \App\Stores\UsersStore($this->sqlManager);
		$route = $request->getAttribute('route');
		$listId = $route->getArgument('list_id');
		$users = $usersStore->byListId($listId, 'json');
		return $this->view->render($response, 'list/sharedwithlist.html.twig', ['users' => $users]);
	}

	public function share(Request $request, Response $response){
		$route = $request->getAttribute('route');
		$listId = $route->getArgument('list_id');
		$userId = $route->getArgument('user_id');
		$listStore = new \App\Stores\ListsStore($this->sqlManager);
		if($listStore->owner($listId) != $this->auth->user())
			return $response->withJson(['response' => 'notOwner']);
		$listStore->share($listId, $userId);
		return $response->withJson(['response' => 'success']);
	}

	public function unshare(Request $request, Response $response){
		$route = $request->getAttribute('route');
		$listId = $route->getArgument('list_id');
		$userId = $route->getArgument('user_id');
		$listStore = new \App\Stores\ListsStore($this->sqlManager);
		if($listStore->owner($listId) != $this->auth->user())
			return $response->withJson(['response' => 'notOwner']);
		$listStore->unshare($listId,$userId);
		return $response->withJson(['response' => 'success']);
	}
}