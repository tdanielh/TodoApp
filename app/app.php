<?php
session_start();

require __DIR__ . '/../vendor/autoload.php';

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
	$view->getEnvironment()->addGlobal('auth', $container->auth);
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

$container['HomeController'] = function($container){
	return new \App\Controllers\HomeController($container);
};

$container['ListController'] = function($container){
	return new \App\Controllers\ListController($container);
};

$container['TaskController'] = function($container){
	return new \App\Controllers\TaskController($container);
};

$container['UserController'] = function($container){
	return new \App\Controllers\UserController($container);
};

$container['auth'] = function ($container) {
	return new App\Auth\Auth($container);
};

require __DIR__ . '/routes/Web.php';

$app->run();