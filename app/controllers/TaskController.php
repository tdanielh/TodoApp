<?php

namespace App\Controllers;

use App\Models\TaskModel;
use App\Stores\ListsStore;
use App\traits\Shareable;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Interfaces\Controller as iController;

class TaskController extends Controller implements iController
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

	public function index(Request $request, Response $response)
	{
		// TODO: Implement index() method.
	}

	public function list(Request $request, Response $response)
	{
		$route = $request->getAttribute('route');
		$listId = $route->getArgument('list_id');
		$usersStore = new \App\Stores\UsersStore($this->sqlManager);
		$listsStore = new \App\Stores\ListsStore($this->sqlManager);
		$tasksStore = new \App\Stores\TasksStore($this->sqlManager);
		$usersStore = new \App\Stores\UsersStore($this->sqlManager);
		$users = $usersStore->byListId($listId);
		$list = $listsStore->byListId($listId);

		if(empty($list) || ($this->auth->user()->getId() != $list->getUserId() && !$users->has($this->auth->user()->getId()))){
			return $this->view->render($response, 'notAllowed.html.twig');
		}
		if(empty($list)){
			return $this->view->render($response, 'list/notFound.html.twig');
		}
		$user = $usersStore->byId($list->getUserId());

		$tasks = $tasksStore->byListId($list->getId());

		return $this->view->render($response, 'tasks.html.twig', ['list' => $list, 'user' => $user, 'tasks' => $tasks]);
	}

	public function create(Request $request, Response $response)
	{
		$post = $request->getParsedBody();
		$description = $post['description'];
		$created = date('Y-m-d H:i:s');
		$status = 'todo';
		$list_id = $post['list_id'];

		if($description == '' || empty($description))
			return $response->withStatus(422)->withJson(['response' => 'error', 'message' => 'Task cannot be empty']);

		$listStore = new \App\Stores\ListsStore($this->sqlManager);
		$list = $listStore->byListId($list_id);
		$task = new TaskModel();
		$task
			->setDescription($description)
			->setCreated($created)
			->setStatus($status)
			->setListId($list->getId());
		$createBuilder = new \Simplon\Mysql\QueryBuilder\CreateQueryBuilder();
		$tasksStore = new \App\Stores\TasksStore($this->sqlManager);

		$task = $tasksStore->create(
			$createBuilder->setModel($task)
		);

		return $this->view->render($response, 'task/task.html.twig', ['task' => $task]);
	}

	public function delete(Request $request, Response $response)
	{
		$listStore = new ListsStore($this->sqlManager);
		$post = $request->getParsedBody();
		$task_id = $post['item_id'];

		$taskStore = new \App\Stores\TasksStore($this->sqlManager);
		$task = $taskStore->taskFromId($task_id);

		$list = $listStore->byListId($task->getListId());

		if($list->getUserId() != $this->auth->user()->getId())
			return $response->withStatus(422)->withJson(['response' => 'error', 'message' => 'You cannot delete this task']);

		$task = $taskStore->delete(
			(new \Simplon\Mysql\QueryBuilder\DeleteQueryBuilder())->setModel($task)->addCondition(\App\Models\TaskModel::COLUMN_ID, $task->getId())
		);
		return json_encode(['status' => 'done']);
	}

	public function update(Request $request, Response $response)
	{
		$post = $request->getParsedBody();
		$task_id = $post['task_id'];

		$taskStore = new \App\Stores\TasksStore($this->sqlManager);
		$task = $taskStore->taskFromId($task_id);
		$newStatus = ($task->getStatus() == 'done') ? 'todo' : 'done';
		$task->setStatus($newStatus);
		$task = $taskStore->update(
			(new \Simplon\Mysql\QueryBuilder\UpdateQueryBuilder())
				->setModel($task)
				->addCondition(\App\Models\TaskModel::COLUMN_ID, $task->getId())
		);
		return json_encode(['status' => $task->getStatus()]);
	}
}