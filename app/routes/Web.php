<?php
$app->get('/', 'HomeController:index')->setName('index');
//$app->get('/lists', 'ListController:list')->setName('lists');
$app->post('/login', 'HomeController:login')->setName('login');
$app->post('/logout', 'HomeController:logout')->setName('logout');
$app->group('/list', function() use ($app, $container){
	$app->post('/create', 'ListController:create')->setName('list.create');
	$app->post('/search', 'ListController:search')->setName('list.search');
	$app->delete('/', 'ListController:delete')->setName('list.delete');
	$app->get('/lists', 'ListController:list')->setName('list.lists');
	$app->group('/{list_id}', function() use ($app, $container){
		$app->get('/','TaskController:list')->setName('list.id');
		$app->get('/sharedwith', 'ListController:sharedWith')->setName('list.sharedWith');
		$app->get('/share/{user_id}', 'ListController:share')->setName('list.share');
		$app->get('/unshare/{user_id}', 'ListController:unshare')->setName('list.unshare');
	});
});

$app->group('/task', function() use ($app, $container){
	$app->post('/create', 'TaskController:create')->setName('task.create');

	$app->post('/update', 'TaskController:update')->setName('task.update.status');

	$app->delete('/','TaskController:delete')->setName('task.delete');
});

$app->group('/users', function() use ($app, $container){
	$app->get('/list', 'UserController:list')->setName('user.list');
});