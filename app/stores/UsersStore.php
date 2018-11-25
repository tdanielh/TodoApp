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
		return 'users';
	}

	/**
	 * @return CrudModelInterface
	 */
	public function getModel()
	{
		return new UserModel();
	}
}