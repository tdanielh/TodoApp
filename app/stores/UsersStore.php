<?php
/**
 * Created by PhpStorm.
 * User: tommy
 * Date: 25-11-2018
 * Time: 20:29
 */

namespace App\Stores;

use App\Models\UserModel;
use App\traits\Crud;
use Simplon\Mysql\Crud\CrudModelInterface;
use Simplon\Mysql\Crud\CrudStore;

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

	public function userByEmail($email){
		$query = 'SELECT * 
				  FROM '.$this->getTableName().'
				  WHERE '.UserModel::COLUMN_EMAIL.' = :email';

		if($result = $this->getCrudManager()->getMysql()->fetchRow($query,['email' => $email]))
			return (new UserModel())->fromArray($result);

		return [];
	}

	public function userById($id){
		$query = 'SELECT *
				  FROM '.$this->getTableName().'
				  WHERE '.UserModel::COLUMN_ID.' = :id';

		if($result = $this->getCrudManager()->getMysql()->fetchRow($query,['id' => $id]))
			return (new UserModel())->fromArray($result);

		return [];
	}
}