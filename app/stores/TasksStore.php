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
		return 'tasks';
	}

	/**
	 * @return CrudModelInterface
	 */
	public function getModel()
	{
		return new TaskModel();
	}

	public function tasksFromListId($id){
		$query = 'SELECT *
				  FROM '. $this->getTableName().'
				  LEFT JOIN list_task
				  ON list_task.task_id = '.$this->getTableName().'.'.ListModel::COLUMN_ID.'
				  WHERE list_task.list_id = :id';
		$tasks = new Collection();

		if($results = $this->getCrudManager()->getMysql()->fetchRowMany($query, ['id' => $id])){
			foreach($results as $result){
				$task = (new TaskModel())->fromArray($result);
				$tasks->set($task->getId(), $task);
			}
			return $tasks;
		}
	}
}