<?php
/**
 * Created by PhpStorm.
 * User: tommy
 * Date: 25-11-2018
 * Time: 21:27
 */

namespace App\traits;

use Simplon\Mysql\QueryBuilder\CreateQueryBuilder;
use Simplon\Mysql\QueryBuilder\DeleteQueryBuilder;
use Simplon\Mysql\QueryBuilder\ReadQueryBuilder;
use Simplon\Mysql\QueryBuilder\UpdateQueryBuilder;

trait Crud
{
	public function create(CreateQueryBuilder $builder)
	{
		$model = $this->crudCreate($builder);
		return $model;
	}

	public function read(?ReadQueryBuilder $builder = null): ?array
	{
		$response = $this->crudRead($builder);
		return $response;
	}

	public function readOne(ReadQueryBuilder $builder)
	{
		$response = $this->crudReadOne($builder);
		return $response;
	}

	public function update(UpdateQueryBuilder $builder)
	{
		$model = $this->crudUpdate($builder);
		return $model;
	}

	public function delete(DeleteQueryBuilder $builder)
	{
		return $this->crudDelete($builder);
	}
}