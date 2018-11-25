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

class ListsStore extends CrudStore
{
	use Crud;

	/**
	 * @return string
	 */

	public function getTableName(): string
	{
		return 'lists';
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
}