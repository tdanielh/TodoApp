<?php
/**
 * Created by PhpStorm.
 * User: tommy
 * Date: 25-11-2018
 * Time: 20:10
 */

namespace App\Models;

use Simplon\Mysql\Crud\CrudModel;
use Slim\Collection;

class TaskModel extends CrudModel
{
	const TABLENAME = 'tasks';

	const COLUMN_ID = 'id';
	const COLUMN_title = 'title';
	const COLUMN_description = 'description';
	const COLUMN_created = 'created';
	const COLUMN_status = 'status';
	const COLUMN_user_id = 'user_id';

	protected $id;
	protected $title;
	protected $description;
	protected $created;
	protected $status;
	protected $user_id;

	public $user;

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param mixed $id
	 * @return TaskModel
	 */
	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param mixed $title
	 * @return TaskModel
	 */
	public function setTitle($title)
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param mixed $description
	 * @return TaskModel
	 */
	public function setDescription($description)
	{
		$this->description = $description;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCreated()
	{
		return $this->created;
	}

	/**
	 * @param mixed $created
	 * @return TaskModel
	 */
	public function setCreated($created)
	{
		$this->created = $created;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @param mixed $status
	 * @return TaskModel
	 */
	public function setStatus($status)
	{
		$this->status = $status;
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
	 * @return TaskModel
	 */
	public function setUserId($user_id)
	{
		$this->user_id = $user_id;
		return $this;
	}

	public function setUser(UserModel $user){
		$this->user = $user;
		return $this;
	}

	public function toArray(bool $snakeCase = true): array
	{
		$array = parent::toArray($snakeCase);
		unset($array['user']);
		return $array;
	}
}