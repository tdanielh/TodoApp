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

require __DIR__ . '/routes/Web.php';

$app->run();