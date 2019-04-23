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

	public function listsByUserId($userId){
		$shareQuery = 'SELECT list_id FROM user_lists where user_lists.user_id = '.$userId;
		$shareResultIds = $this->getCrudManager()->getMysql()->fetchColumnMany($shareQuery, ['user_id' => $userId]);

		$query = 'SELECT * 
				  FROM '. $this->getTableName().'
				  WHERE '.$this->getTableName().'.'.ListModel::COLUMN_USER_ID.' = :user_id OR '.$this->getTableName().'.'.ListModel::COLUMN_ID.' IN (:user_ids)
				  ORDER BY id desc';

		$lists = new Collection();
		if($results = $this->getCrudManager()->getMysql()->fetchRowMany($query, ['user_id' => $userId, 'user_ids' => $shareResultIds])){
			foreach($results as $result){
				$list = (new ListModel())->fromArray($result);
				$lists->set($list->getId(), $list);
			}
			return $lists;
		}
		return [];
	}

	public function byListId($list_id){
		$query = 'SELECT *
				  FROM '.$this->getTableName().'
				  WHERE '.$this->getTableName().'.'.ListModel::COLUMN_ID.' = :list_id';

		if($result = $this->getCrudManager()->getMysql()->fetchRow($query, ['list_id' => $list_id]))
			return (new ListModel())->fromArray($result);
		return [];
	}

	public function owner($list_id){
		$result = $this->getCrudManager()->getMysql()->fetchRow('SELECT users.* FROM lists LEFT JOIN users ON users.id = lists.user_id where lists.id = :list_id', ['list_id' => $list_id]);
		$result = (new UserModel())->fromArray($result);
		return $result;
	}

	public function share($list_id, $user_id){
		$this->getCrudManager()->getMysql()->insert('user_lists', ['user_id' => $user_id, 'list_id'=>$list_id]);
	}

	public function unshare($list_id, $user_id){
		$this->getCrudManager()->getMysql()->delete('user_lists', ['user_id' => $user_id, 'list_id'=>$list_id]);
	}
}