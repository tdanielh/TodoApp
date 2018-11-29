<?php
/**
 * Created by PhpStorm.
 * User: tommy
 * Date: 25-11-2018
 * Time: 20:29
 */

namespace App\Stores;

use App\Models\ListModel;
use App\traits\Crud;
use Simplon\Mysql\Crud\CrudModelInterface;
use Simplon\Mysql\Crud\CrudStore;
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

	public function listsFromUserId($userId){
		$query = 'SELECT * 
				  FROM '. $this->getTableName().'
				  WHERE '.$this->getTableName().'.'.ListModel::COLUMN_USER_ID.' = :user_id
				  ORDER BY id desc';

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