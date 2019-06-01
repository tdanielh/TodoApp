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

	public function listsByUserId($userId, $type = []){
		$shareResultIds = [0];

		if(!$type){
			$type = [];
		}

		if(!in_array('private', $type))
		{
			$shareQuery = 'SELECT list_id FROM user_lists where user_lists.user_id = '.$userId;
			$shareResultIds = $this->getCrudManager()->getMysql()->fetchColumnMany($shareQuery, ['user_id' => $userId]);
		}

		$params = ['user_id' => $userId];
		$query = 'SELECT * 
				  FROM '. $this->getTableName();

		if(in_array('private', $type)){
			$query .= ' LEFT JOIN user_lists ON user_lists.list_id = lists.id ';
		}

		$query .= ' WHERE '.$this->getTableName().'.'.ListModel::COLUMN_USER_ID.' = :user_id';

		if(in_array('private', $type)){
			$query .= ' AND user_lists.list_id IS NULL';
		}

		if(!in_array('private', $type)){
			$query .= ' OR '.$this->getTableName().'.'.ListModel::COLUMN_ID.' IN (:user_ids)';
			$params['user_ids'] = $shareResultIds;
		}
		$query .= ' ORDER BY lists.id desc';

		$lists = new Collection();
		if($results = $this->getCrudManager()->getMysql()->fetchRowMany($query, $params)){
			foreach($results as $result){
				$list['list'] = (new ListModel())->fromArray($result);
				$ownerQuery = $this->getCrudManager()->getMysql()->fetchRow('SELECT * FROM users where id = :user_id', ['user_id' => $list['list']->getUserId()]);
				$shares = $this->getCrudManager()->getMysql()->fetchRow('SELECT COUNT(user_lists.list_id) as shares FROM lists LEFT JOIN user_lists ON user_lists.list_id = lists.id WHERE lists.id = :list_id AND user_lists.list_id IS NOT NULL', ['list_id' => $list['list']->getId()]);
				$owner = "";
				if($ownerQuery){
					$owner = (new UserModel())->fromArray($ownerQuery);
				}

				$list['owner'] = $owner;
				$list['shares'] = $shares['shares'];
				$lists->set($list['list']->getId(), $list);
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