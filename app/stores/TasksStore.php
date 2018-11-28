<?php
/**
 * Created by PhpStorm.
 * User: tommy
 * Date: 25-11-2018
 * Time: 20:29
 */

namespace App\Stores;

use App\Models\ListModel;
use App\Models\TaskModel;
use App\Models\UserModel;
use App\traits\Crud;
use Simplon\Mysql\Crud\CrudModelInterface;
use Simplon\Mysql\Crud\CrudStore;
use Simplon\Mysql\MysqlException;
use Simplon\Mysql\QueryBuilder\CreateQueryBuilder;
use Simplon\Mysql\QueryBuilder\DeleteQueryBuilder;
use Simplon\Mysql\QueryBuilder\ReadQueryBuilder;
use Simplon\Mysql\QueryBuilder\UpdateQueryBuilder;
use Slim\Collection;

class TasksStore extends CrudStore
{
	use Crud;
	/**
	 * @return string
	 */
	public function getTableName(): string
	{
		return TaskModel::TABLENAME;
	}

	/**
	 * @return CrudModelInterface
	 */
	public function getModel()
	{
		return new TaskModel();
	}

	public function tasksFromListId($id){
		$listQuery = 'SELECT *
				  FROM '. $this->getTableName().'
				  LEFT JOIN list_task
				  ON list_task.task_id = '.$this->getTableName().'.'.ListModel::COLUMN_ID.'
				  WHERE list_task.list_id = :id';
		$tasks = new Collection();
		$results = $this->getCrudManager()->getMysql()->fetchRowMany($listQuery, ['id' => $id]);
		if($results){
			foreach($results as $result){
				$task = (new TaskModel())->fromArray($result);
				$tasks->set($task->getId(), $task);
			}
		}

		$userIds = [];
		foreach($tasks->all() as $task){
			$userId = $task->getUserid();
			$usersArr[$userId] = $userId;
		}
		var_dump($usersArr);
		$userIds = join(',',  $usersArr);
		$userQuery = 'SELECT * FROM users WHERE id IN(:user_ids)';
		$users = $this->getCrudManager()->getMysql()->fetchRowMany($userQuery, ['user_ids' => $userIds]);

		foreach($users as $user){
			$usersArr[$user['id']] = $user;
		}

		foreach($tasks as $task){
			if(isset($usersArr[$task->getUserId()])){
				$user = (new UserModel())->fromArray($usersArr[$task->getUserId()]);
				$task->setUser($user);
			}
		}
		return $tasks;
	}


}