<?php
/**
 * Created by PhpStorm.
 * User: tommy
 * Date: 25-11-2018
 * Time: 20:29
 */

namespace App\Stores;

use App\Models\TaskModel;
use App\traits\Crud;
use Simplon\Mysql\Crud\CrudModelInterface;
use Simplon\Mysql\Crud\CrudStore;
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

	public function byListId($list_id){
		$tasksQuery = 'SELECT *
				  FROM '. $this->getTableName().'
				  WHERE '. $this->getTableName().'.'.TaskModel::COLUMN_LIST_ID.' = :list_id
				  ORDER BY '.$this->getTableName().'.'.TaskModel::COLUMN_ID.' DESC';
		$tasks = new Collection();
		$taskresults = $this->getCrudManager()->getMysql()->fetchRowMany($tasksQuery, ['list_id' => $list_id]);
		if($taskresults){
			foreach($taskresults as $taskresult){
				$task = (new TaskModel())->fromArray($taskresult);
				$tasks->set($task->getId(), $task);
			}

			return $tasks;
		}
		return [];
	}

	public function taskFromId($task_id){
		$taskQuery = 'SELECT * 
					  FROM '.$this->getTableName().' 
					  WHERE '.$this->getTableName().'.'.TaskModel::COLUMN_ID.' = :task_id';
		$result = $this->getCrudManager()->getMysql()->fetchRow($taskQuery, ['task_id' => $task_id]);
		if($result)
			return (new TaskModel())->fromArray($result);
		return [];
	}

}