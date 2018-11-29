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

class UsersStore extends CrudStore
{
	use Crud;

	/**
	 * @return string
	 */
	public function getTableName(): string
	{
		return UserModel::TABLENAME;
	}

	/**
	 * @return CrudModelInterface
	 */
	public function getModel()
	{
		return new UserModel();
	}

	public function getUserFromListId($list_id){
		$query = 'SELECT * 
				  FROM '.$this->getTableName().'
				  LEFT JOIN list_user
				  ON list_user.user_id = :list_id
				  LIMIT 1';
		if($result = $this->getCrudManager()->getMysql()->fetchRow($query, ['list_id' => $list_id]))
			return (new UserModel())->fromArray($result);

		return [];
	}

	public function usersFromTasks(Collection $tasks){
		$taskIds = [];
		foreach($tasks->all() as $task){
			$taskIds[] = $task->getId();
		}
		$query = 'SELECT * 
				  FROM '.$this->getTableName().'
				  LEFT JOIN '.TaskModel::TABLENAME.'
				  ON '.TaskModel::TABLENAME.'.'.TaskModel::COLUMN_user_id.'
				  WHERE '.TaskModel::TABLENAME.'.'.TaskMOdel::COLUMN_user_id.' IN('.$taskIds.')';
		return $query;
	}


}