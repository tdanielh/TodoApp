<?php
/**
 * Created by PhpStorm.
 * User: tommy
 * Date: 25-11-2018
 * Time: 20:10
 */

namespace App\Models;

use Simplon\Mysql\Crud\CrudModel;

class UserModel extends CrudModel
{
	const TABLENAME = 'users';

	const COLUMN_ID = 'id';
	const COLUMN_EMAIL = 'email';
	const COLUMN_PASSWORD = 'password';

	protected $id;
	protected $email;
	protected $password;

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param mixed $id
	 * @return UserModel
	 */
	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * @param mixed $email
	 * @return UserModel
	 */
	public function setEmail($email)
	{
		$this->email = $email;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * @param mixed $password
	 * @return UserModel
	 */
	public function setPassword($password)
	{
		$this->password = $password;
		return $this;
	}


}