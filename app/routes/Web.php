<?php
$app->get('/', 'HomeController:index')->setName('index');
//$app->get('/lists', 'ListController:list')->setName('lists');
$app->post('/login', 'HomeController:login')->setName('login');
$app->post('/logout', 'HomeController:logout')->setName('logout');
$app->group('/list', function() use ($app, $container){
	$app->post('/create', 'ListController:create')->setName('list.create');
	$app->delete('/', 'ListController:delete')->setName('list.delete');
	$app->get('/{list_id}','TaskController:list')->setName('list.id');
});

$app->group('/task', function() use ($app, $container){
	$app->post('/create', 'TaskController:create')->setName('task.create');

	$app->post('/update', 'TaskController:update')->setName('task.update.status');

	$app->delete('/','TaskController:delete')->setName('task.delete');
});