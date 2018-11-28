<?php
/**
 * Created by PhpStorm.
 * User: tommy
 * Date: 14-09-2017
 * Time: 19:04
 */

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
session_start();
include "vendor/autoload.php";
$app = new \Slim\App(['settings' => ['displayErrorDetails' => true]]);

$container = $app->getContainer();
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('views/',array(
        'debug' => true,
        // ...
    ));
    $view->addExtension(new \Slim\Views\TwigExtension(
        $container['router'],
        $container['request']->getUri()
    ));
    $view->addExtension(new Twig_Extension_Debug());

    return $view;
};

$container['PDOConnector'] = function($container){
	$connector = new \Simplon\Mysql\PDOConnector('localhost', 'root', '', 'todoapp');
	
    return $connector;
};

$container['sqlManager'] = function($container){
    $sqlManager = new \Simplon\Mysql\Mysql($container['PDOConnector']->connect());
    return $sqlManager;
};

$app->get('/', function(){
	var_dump('test');
});

$app->get('/lists',function() use ($container){
	$store = new \App\Stores\ListsStore($container['sqlManager']);
	$lists = $store->lists(1);

	var_dump($lists);

});
$app->group('/user/{user_id}', function() use ($app, $container){
	$app->group('/{list_id}', function() use ($app, $container){
		$app->post('/delete', function(Request $request) use ($container){

		});

		$app->post('/update', function(Request $request) use ($container){

		});
		$app->get('/', function(Request $request) use ($container){
			$route = $request->getAttribute('route');
			$listId = $route->getArgument('list_id');
			$tasksStore = new \App\Stores\TasksStore($container['sqlManager']);
			var_dump($tasksStore->tasksFromListId($listId));
		});
		$app->group('/task', function() use ($app, $container){
			$app->get('/new', function() use ($container){

			});

			$app->post('/create', function() use ($container){

			});

			$app->group('/{task_id}', function() use ($app, $container){
				$app->get('/', function(Request $request) use ($container){
					//TODO show task.
				});

				$app->get('/edit', function(Request $request) use ($container){

				});

				$app->post('/update', function(Request $request) use ($container){

				});
			});
		});

		$app->post('/share', function(Request $request) use ($container){

		});
	});
});

$app->get('/users', function(){

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
		$userId = $post['user_id'];
		$model = new \App\Models\ListModel();
		$model
			->setName($title)
			->setUserId($userId);

		$createBuilder = new \Simplon\Mysql\QueryBuilder\CreateQueryBuilder();

		$listStore = new \App\Stores\ListsStore($container['sqlManager']);

		$listStore->create(
			$createBuilder->setModel($model)
		);
	})->setName('list.create');

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

		return $this->view->render($response, 'list.html.twig', ['list' => $list, 'user' => $user, 'tasks' => $tasks]);
	});
});

$app->group('/task', function() use ($app, $container){
	$app->post('/create', function(Request $request, Response $response) use ($container){
		$post = $request->getParsedBody();
		$title = $post['title'];
		$description = $post['description'];
		$created = date('Y-m-d H:i:s');
		$status = 'todo';
		$user_id = $post['user_id'];

		$model = new App\Models\TaskModel();
		$model
			->setTitle($title)
			->setDescription($description)
			->setCreated($created)
			->setStatus($status)
			->setUserId($user_id);
		$createBuilder = new \Simplon\Mysql\QueryBuilder\CreateQueryBuilder();
		$tasksStore = new \App\Stores\TasksStore($container['sqlManager']);

		$tasksStore->create(
			$createBuilder->setModel($model)
		);

		var_dump($model);
	})->setName('task.create');
});

$app->run();