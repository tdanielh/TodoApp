<?php
/**
 * Created by PhpStorm.
 * User: tommy
 * Date: 25-11-2018
 * Time: 20:10
 */

namespace App\Models;

use Simplon\Mysql\Crud\CrudModel;

class ListModel extends CrudModel
{
	const TABLENAME = 'lists';

	const COLUMN_ID = 'id';
	const COLUMN_NAME = 'name';
	const COLUMN_USER_ID = 'user_id';

	protected $id;
	protected $name;
	protected $user_id;

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param mixed $id
	 * @return ListModel
	 */
	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param mixed $name
	 * @return ListModel
	 */
	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getUserId()
	{
		return $this->user_id;
	}

	/**
	 * @param mixed $user_id
	 * @return ListModel
	 */
	public function setUserId($user_id)
	{
		$this->user_id = $user_id;
		return $this;
	}


}