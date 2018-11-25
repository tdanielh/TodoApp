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

/*$config = array(
	'host'       => 'localhost',
	'user'       => 'root',
	'password'   => '',
	'database'   => 'screenjunkie_me'
);*/
    $config = array(
        'host'       => 'localhost',
        'user'       => 'root',
        'password'   => '',
        'database'   => 'todoapp'
    );

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

$app->get('/list/{id}/tasks', function(Request $request) use ($container){
	$route = $request->getAttribute('route');
	$listId = $route->getArgument('id');
	$tasksStore = new \App\Stores\TasksStore($container['sqlManager']);
	var_dump($tasksStore->tasksFromListId($listId));
});

$app->run();

exit;