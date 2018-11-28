<?php
/**
 * Created by PhpStorm.
 * User: tommy
 * Date: 25-11-2018
 * Time: 20:29
 */

namespace App\Stores;

use App\Models\ListModel;
use App\Models\UserModel;
use App\traits\Crud;
use Simplon\Mysql\Crud\CrudModelInterface;
use Simplon\Mysql\Crud\CrudStore;
use Simplon\Mysql\Mysql;
use Simplon\Mysql\MysqlException;
use Slim\Collection;

class ListsStore extends CrudStore
{
	use Crud;

	/**
	 * @return string
	 */

	public function getTableName(): string
	{
		return ListModel::TABLENAME;
	}

	/**
	 * @return CrudModelInterface
	 */
	public function getModel()
	{
		return new ListModel();
	}

	public function lists($id){
		$query = 'SELECT * 
				  FROM ' .$this->getTableName().' 
				  LEFT JOIN list_user 
				  ON list_user.user_id = '.$this->getTableName().'.'.ListModel::COLUMN_USER_ID.'
				  LEFT JOIN '.UserModel::TABLENAME.'
				  ON users.'.UserModel::COLUMN_ID.'
				  WHERE  '.$this->getTableName().'.'.ListModel::COLUMN_USER_ID.' = '.$id;

		var_dump($query);

		return $id;
	}

	public function listsFromUserId($userId){
		$query = 'SELECT * 
				  FROM '. $this->getTableName().'
				  LEFT JOIN list_user
				  ON list_user.list_id = '.$this->getTableName().'.'.ListModel::COLUMN_USER_ID.'
				  WHERE list_user.user_id = :user_id';

		$lists = new Collection();
		if($results = $this->getCrudManager()->getMysql()->fetchRowMany($query, ['user_id' => $userId])){
			foreach($results as $result){
				$list = (new ListModel())->fromArray($result);
				$lists->set($list->getId(), $list);
			}
			return $lists;
		}
		return [];
	}

	public function listFromListId($listId){
		$query = 'SELECT *
				  FROM '.$this->getTableName().'
				  WHERE '.$this->getTableName().'.'.ListModel::COLUMN_ID.' = :list_id';

		if($result = $this->getCrudManager()->getMysql()->fetchRow($query, ['list_id' => $listId]))
			return (new ListModel())->fromArray($result);
		return [];
	}
}