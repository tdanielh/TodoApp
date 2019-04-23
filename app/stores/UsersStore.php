<?php
/**
 * Created by PhpStorm.
 * User: tommy
 * Date: 25-11-2018
 * Time: 20:29
 */

namespace App\stores;

use App\Models\UserModel;
use App\traits\Crud;
use Simplon\Mysql\Crud\CrudModelInterface;
use Simplon\Mysql\Crud\CrudStore;
use Slim\Collection;

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

	public function byEmail($email){
		$query = 'SELECT * 
				  FROM '.$this->getTableName().'
				  WHERE '.UserModel::COLUMN_EMAIL.' = :email';

		if($result = $this->getCrudManager()->getMysql()->fetchRow($query,['email' => $email]))
			return (new UserModel())->fromArray($result);

		return [];
	}

	public function byId($id){
		$query = 'SELECT *
				  FROM '.$this->getTableName().'
				  WHERE '.UserModel::COLUMN_ID.' = :id';

		if($result = $this->getCrudManager()->getMysql()->fetchRow($query,['id' => $id]))
			return (new UserModel())->fromArray($result);

		return [];
	}

	public function byListId($list_id, $type = null){
		$users = new Collection();
		$query = 'SELECT '.$this->getTableName().'.* FROM '.$this->getTableName().' JOIN user_lists ON user_lists.user_id = users.id WHERE user_lists.list_id = :list_id';
		if($results = $this->getCrudManager()->getMysql()->fetchRowMany($query, ['list_id' => $list_id])){
			foreach($results as $result){
				if($type == null)
					$users->set($result['id'], (new UserModel())->fromArray($result));
				else if($type != null)
					$users->set($result['id'], $result);
			}
			if($type == null)
				return $users;
			return $users->all();
		}
		return [];
	}

	public function list($search = [], $type = null){
		$users = new Collection();
		$query = 'SELECT *
				  FROM '.$this->getTableName().' WHERE name LIKE :name';
		if($results = $this->getCrudManager()->getMysql()->fetchRowMany($query,['name' => $search['name'].'%'])){
			foreach($results as $result){
				if($type == null)
					$users->set($result['id'], (new UserModel())->fromArray($result));
				else if($type != null)
					$users->set($result['id'], $result);
			}
			return $users->all();
		}


		return [];
	}
}