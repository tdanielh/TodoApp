<?php
/**
 * Created by PhpStorm.
 * User: tommy
 * Date: 05-12-2018
 * Time: 20:27
 */

namespace App\Services;

class ListService
{
	protected $container;
	public function __construct($container)
	{
		$this->container = $container;
	}

	public function getContainer(){
		return $this->container;
	}
}