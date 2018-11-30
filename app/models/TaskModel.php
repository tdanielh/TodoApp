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
	const COLUMN_TITLE = 'title';
	const COLUMN_DESCRIPTION = 'description';
	const COLUMN_CREATED = 'created';
	const COLUMN_STATUS = 'status';
	const COLUMN_LIST_ID = 'list_id';

	protected $id;
	protected $title;
	protected $description;
	protected $created;
	protected $status;
	protected $list_id;

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
	public function getListId()
	{
		return $this->list_id;
	}

	/**
	 * @param mixed $list_id
	 */
	public function setListId($list_id)
	{
		$this->list_id = $list_id;
		return $this;
	}
}