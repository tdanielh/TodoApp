<?php

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

$app->get('/', 'HomeController:index')->setName('index');

$app->get('/lists',function(Request $request,Response $response) use ($container){
	$store = new \App\Stores\ListsStore($container['sqlManager']);
	//TODO use auth id
	$lists = $store->listsFromUserId(1);

	return $this->view->render($response, 'lists.html.twig', ['lists'=>$lists]);

})->setName('lists');
$app->group('/user/{user_id}', function() use ($app, $container){
	$app->group('/{list_id}', function() use ($app, $container){
		$app->get('/', function(Request $request) use ($container){
			$route = $request->getAttribute('route');
			$listId = $route->getArgument('list_id');
			$tasksStore = new \App\Stores\TasksStore($container['sqlManager']);
			var_dump($tasksStore->tasksFromListId($listId));
		});
		$app->group('/task', function() use ($app, $container){
			$app->get('/new', function() use ($container){
				//TODO needed?
			});

			$app->post('/create', function() use ($container){
				//TODO Needed?
			});

			$app->group('/{task_id}', function() use ($app, $container){
				$app->get('/', function(Request $request) use ($container){
					//TODO show task. Needed?
				});

				$app->get('/edit', function(Request $request) use ($container){
					//TODO needed?
				});

				$app->post('/update', function(Request $request) use ($container){
					//TODO needed?
				});
			});
		});

		$app->post('/share', function(Request $request) use ($container){

		});
	});
});

$app->get('/users', function(){
	//TODO needed?
});

$app->group('/user/{user_id}', function() use ($app, $container){
	$app->get('/',function(Request $request) use ($container){

	});

	$app->get('/lists', function(Request $request) use ($container){
		$route = $request->getAttribute('route');
		$userId = $route->getArgument('user_id');
		$listsStore = new \App\Stores\ListsStore($container['sqlManager']);
		$lists = $listsStore->listsFromUserId($userId);
		foreach($lists as $list){
			var_dump($list);
		}
	});

	$app->get('/newlist', function(Request $request, Response $response) use ($container){
		$route = $request->getAttribute('route');
		$userId = $route->getArgument('user_id');
		return $this->view->render($response, "list/new.html.twig", ['user_id' => $userId]);
	});

});

$app->group('/list', function() use ($app, $container){
	$app->post('/create', function(Request $request, Response $response) use ($container){
		$post = $request->getParsedBody();
		$title = $post['title'];

		if($title == '' || empty($title))
			return $response->withStatus(422)->withJson(['response' => 'error', 'message' => 'Title cannot be empty']);

		//TODO make auth id
		$userId = 1;
		$model = new \App\Models\ListModel();
		$model
			->setName($title)
			->setUserId($userId);

		$createBuilder = new \Simplon\Mysql\QueryBuilder\CreateQueryBuilder();

		$listStore = new \App\Stores\ListsStore($container['sqlManager']);

		$list = $listStore->create(
			$createBuilder->setModel($model)
		);

		return $this->view->render($response, 'list/list.html.twig', ['list' => $list]);
	})->setName('list.create');

	$app->delete('/', function(Request $request, Response $response) use ($container){
		$post = $request->getParsedBody();
		$list_id = $post['item_id'];

		$listsStore = new \App\Stores\ListsStore($container['sqlManager']);
		$list = $listsStore->listFromListId($list_id);

		$list = $listsStore->delete(
			(new \Simplon\Mysql\QueryBuilder\DeleteQueryBuilder())->setModel($list)->addCondition(\App\Models\ListModel::COLUMN_ID, $list->getId())
		);
		return json_encode(['status' => 'done']);
	})->setName('list.delete');

	$app->get('/{list_id}',function(Request $request,Response $response) use ($container){
		$route = $request->getAttribute('route');
		$listId = $route->getArgument('list_id');
		$usersStore = new \App\Stores\UsersStore($container['sqlManager']);
		$listsStore = new \App\Stores\ListsStore($container['sqlManager']);
		$tasksStore = new \App\Stores\TasksStore($container['sqlManager']);

		$list = $listsStore->listFromListId($listId);

		if(empty($list)){
			return $this->view->render($response, 'list/notFound.html.twig');
		}
		$user = $usersStore->getUserFromListId($listId);
		$tasks = $tasksStore->tasksFromListId($list->getId());

		return $this->view->render($response, 'tasks.html.twig', ['list' => $list, 'user' => $user, 'tasks' => $tasks]);
	})->setName('list.id');
});

$app->group('/task', function() use ($app, $container){
	$app->post('/create', function(Request $request, Response $response) use ($container){
		$post = $request->getParsedBody();
		$description = $post['description'];
		$created = date('Y-m-d H:i:s');
		$status = 'todo';
		$user_id = $post['user_id'];
		$list_id = $post['list_id'];

		if($description == '' || empty($description))
			return $response->withStatus(422)->withJson(['response' => 'error', 'message' => 'Task cannot be empty']);

		$listStore = new \App\Stores\ListsStore($container['sqlManager']);
		$listModel = $listStore->listFromListId($list_id);

		$taskModel = new App\Models\TaskModel();
		$taskModel
			->setDescription($description)
			->setCreated($created)
			->setStatus($status)
			->setUserId($user_id);
		$createBuilder = new \Simplon\Mysql\QueryBuilder\CreateQueryBuilder();
		$tasksStore = new \App\Stores\TasksStore($container['sqlManager']);

		$task = $tasksStore->create(
			$createBuilder->setModel($taskModel),
			$listModel
		);

		return $this->view->render($response, 'task/task.html.twig', ['task' => $task]);
	})->setName('task.create');

	$app->post('/update', function(Request $request, Response $response) use ($container){
		$post = $request->getParsedBody();
		$task_id = $post['task_id'];

		$taskStore = new \App\Stores\TasksStore($container['sqlManager']);
		$task = $taskStore->taskFromId($task_id);
		$newStatus = ($task->getStatus() == 'done') ? 'todo' : 'done';
		$task->setStatus($newStatus);
		$task = $taskStore->update(
			(new \Simplon\Mysql\QueryBuilder\UpdateQueryBuilder())
				->setModel($task)
				->addCondition(\App\Models\TaskModel::COLUMN_ID, $task->getId())
		);
		return json_encode(['status' => $task->getStatus()]);
	})->setName('task.update.status');

	$app->delete('/',function (Request $request, Response $response) use ($container){
		$post = $request->getParsedBody();
		$task_id = $post['item_id'];

		$taskStore = new \App\Stores\TasksStore($container['sqlManager']);
		$task = $taskStore->taskFromId($task_id);

		$task = $taskStore->delete(
			(new \Simplon\Mysql\QueryBuilder\DeleteQueryBuilder())->setModel($task)->addCondition(\App\Models\TaskModel::COLUMN_ID, $task->getId())
		);
		return json_encode(['status' => 'done']);


	})->setName('task.delete');
});